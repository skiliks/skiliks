/*global Backbone, _ */
(function () {
    "use strict";
    window.SKMailClientView = Backbone.View.extend({
        
        mailClientScreenID: 'mailEmulatorMainScreen',
        
        mailClientFoldersListId: 'MailClient_FolderLabels',
        
        mailClientContentBlockId: 'MailClient_ContentBlock',
        
        mailClientIncomeFolderListId: 'MailClient_IncomeFolder_List',
        
        mailClientIncomeFolderEmailPreviewId: 'MailClient_IncomeFolder_EmailPreview',
        
        mailClientReadEmailContentBoxId: 'MailClient_ReadEmail_Content',
        
        mailClient: undefined,
        
        setMailClient: function(mailClient) {
            this.mailClient = mailClient;
        },
        
        /**(
         * Display (create if not exist) MailClient screen base
         */
        renderMailClientScreenBase: function () {
            
            var existsMailClientDiv = $('#' + this.mailClientScreenID);
            
            if (0 == existsMailClientDiv.length) {
                // get template
                var mailClientWindowBasicHtml = _.template($('#MailClient_BasicHtml').html(), {
                    id: this.mailClientScreenID,
                    contentBlockId: this.mailClientContentBlockId
                });

                // set window position
                $(mailClientWindowBasicHtml).css({
                    'z-index'  : 50,
                    'top'      : '35px',
                    'left'     : '283px',
                    'position' : 'absolute'
                });

                // append to <body>
                $('body').append(mailClientWindowBasicHtml);
            } else {
                existsMailClientDiv.show();
            }
        },
        
        doGetEmailDetails: function (emailId) {
            // do we have full data for current email ? {
            var email = SKApp.user.simulation.mailClient.getIncomeFolder().getEmailByMySqlId(emailId);
            
            if ('undefined' == typeof email) {
                throw 'Try to render unexistent email ' + emailId + '.';
            }
            
            this.mailClient.setActiveEmail(email);
            
            if ('undefined' != typeof email.text && 
                'undefined' != typeof email.attachment) {
                // if YES - just render it
                SKApp.user.simulation.mailClient.viewObject.renderEmaiPreviewScreen(
                    email,
                    SKApp.user.simulation.mailClient.viewObject.mailClientIncomeFolderEmailPreviewId
                );
                return;
            }
            // do we have full data for current email ? }
            
            // if NOT - send request to get copies string and attachment for current email
            SKApp.server.api(
                'mail/getMessage',
                { sid: session.getSid(),
                  emailId: emailId 
                }, 
                function (responce) {
                    if (1 == responce.result) {
                        // update email {
                        var email = SKApp.user.simulation.mailClient.getIncomeFolder()
                            .getEmailByMySqlId(responce.data.id);

                        // update attachment object
                        var attachment   = new SKAttachment();
                        attachment.id    = responce.data.attachments.id;
                        attachment.label = responce.data.attachments.name;
                        email.attachment = attachment;

                        // update Copy to: string
                        email.copyToString = responce.data.copies;
                        
                        email.text = responce.data.message;
                        // update email }
                        
                        // render preview
                        SKApp.user.simulation.mailClient.viewObject.renderEmaiPreviewScreen(
                            email, 
                            SKApp.user.simulation.mailClient.viewObject.mailClientIncomeFolderEmailPreviewId
                       );
                    }
                });
        },
        
        /**
         */
        hideMailClientScreen: function() {            
            $('#' + this.mailClientScreenID).hide();
        },
        
        updateFolderLabels: function()
        {
            var html = '';
            
            for(var alias in this.mailClient.folders) {
                
                var isActiveCssClass = '';
                if (this.mailClient.folders[alias].isActive) {
                    isActiveCssClass = ' active ';
                }
                
                html += _.template($('#MailClient_FolderLabel').html(), {
                    label:            this.mailClient.folders[alias].name,
                    isActiveCssClass: isActiveCssClass,
                    counter:          this.mailClient.folders[alias].emails.length,
                    alias:            alias
                });
            }
            
            $('#' + this.mailClientFoldersListId).html(html);
        },
        
        updateIncomeListView: function() {
            // generate emails list {
            var emailsList = '';
            
            var incomingEmails = this.mailClient.folders[this.mailClient.aliasFolderIncome].emails; // to make code shorter
            
            for (var key in incomingEmails) {
                
                // check is email active
                var isActiveCssClass = '';
                if (incomingEmails[key].mySqlId == this.mailClient.activeEmail.mySqlId) {
                    // why 2 CSS classes? - this is works
                    isActiveCssClass = ' mail-emulator-received-list-string-selected active '; 
                }    
                
                // generate HTML by template
                emailsList += _.template($('#MailClient_IncomeEmailLine').html(),{
                    emailMySqlId:       incomingEmails[key].mySqlId,
                    senderName:         incomingEmails[key].senderNameString,
                    subject:            incomingEmails[key].subject.text,
                    sendedAt:           incomingEmails[key].sendedAt,
                    isHasAttachmentCss: incomingEmails[key].getIsHasAttachmentCss(),
                    isReadedCssClass:   incomingEmails[key].getIsReadedCssClass(),
                    isActiveCssClass:   isActiveCssClass
                }); 
            }         
            // generate emails list }
            
            // add emails list
            $('#' + this.mailClientIncomeFolderListId + ' table').html(emailsList);
            
            $('.email-list-line').click(function(event){
                // if user click on same email line twise - open read email screen
                if ($(event.currentTarget).data().emailId == SKApp.user.simulation.mailClient.activeEmail.mySqlId) {
                    SKApp.user.simulation.mailClient.renderReadEmailScreen(
                        $(event.currentTarget).data().emailId
                    );   
                } else {
                    // if user clicks on different email lines - activate ckicked line email
                    SKApp.user.simulation.mailClient.viewObject.doGetEmailDetails(
                        $(event.currentTarget).data().emailId
                    );
                }
            });
        },
        
        renderIncomeFolder: function() {
            // set HTML sceleton {
            var sceleton = _.template($('#MailClient_IncomeFolderSceleton').html(),{
                listId: this.mailClientIncomeFolderListId,
                emailPreviewId: this.mailClientIncomeFolderEmailPreviewId
            });
            
            $('#' + this.mailClientContentBlockId).html(sceleton);
            // set HTML sceleton }
            
            this.updateIncomeListView();
            
            // render preview email
            if (undefined != this.mailClient.activeEmail) {
                this.doGetEmailDetails(this.mailClient.activeEmail.mySqlId);
            }
        },
        
        renderEmaiPreviewScreen: function(email, id) {
            this.updateIncomeListView();
            
            var emailPreviewTemplate = _.template($('#MailClient_EmailPreview').html(),{
                emailMySqlId:       email.mySqlId,
                senderName:         email.senderNameString,
                recipientName:      this.mailClient.heroNameEmail,
                copyNamesLine:      email.copyToString,
                subject:            email.subject.text,
                text:               email.text,
                sendedAt:           email.sendedAt,
                isHasAttachmentCss: email.getIsHasAttachmentCss(),
                isReadedCssClass:   email.getIsReadedCssClass(),
                attachmentFileName: email.attachment.label,
                attachmentId:       email.attachment.id
            });
            
            $('#' + id).html(emailPreviewTemplate);
        },
        
        renderReadEmail: function(email) {
            // set HTML sceleton {
            var sceleton = _.template($('#MailClient_ReadEmailSceleton').html(),{
                emailPreviewId: this.mailClientReadEmailContentBoxId
            });
            
            $('#' + this.mailClientContentBlockId).html(sceleton);
            // set HTML sceleton } 
            
            this.renderEmaiPreviewScreen(email, this.mailClientReadEmailContentBoxId);
        }
    });
})();