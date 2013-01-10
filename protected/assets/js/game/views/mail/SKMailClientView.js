/*global Backbone, _, SKApp, SKAttachment */
(function () {
    "use strict";
    window.SKMailClientView = Backbone.View.extend({

        mailClientScreenID:'mailEmulatorMainScreen',

        mailClientFoldersListId:'MailClient_FolderLabels',

        mailClientContentBlockId:'MailClient_ContentBlock',

        mailClientIncomeFolderListId:'MailClient_IncomeFolder_List',

        mailClientInboxFolderEmailPreviewId:'MailClient_IncomeFolder_EmailPreview',

        mailClientReadEmailContentBoxId:'MailClient_ReadEmail_Content',

        mailClient:undefined,

        /**
         * Used to add reverce link from view to it`s model
         * @param mailClient SKMailClient
         */
        setMailClient:function (mailClient) {
            this.mailClient = mailClient;
        },

        /**(
         * Display (create if not exist) MailClient screen base
         */
        renderMailClientScreenBase:function () {

            var existsMailClientDiv = $('#' + this.mailClientScreenID);

            if (0 === existsMailClientDiv.length) {
                // get template
                var mailClientWindowBasicHtml = _.template($('#MailClient_BasicHtml').html(), {
                    id:this.mailClientScreenID,
                    contentBlockId:this.mailClientContentBlockId
                });

                // set window position
                $(mailClientWindowBasicHtml).css({
                    'z-index':50,
                    'top':'35px',
                    'left':'283px',
                    'position':'absolute'
                });

                // append to <body>
                $('body').append(mailClientWindowBasicHtml);
            } else {
                existsMailClientDiv.show();
            }
        },

        /**
         * Used to get data for email preview
         * @todo move to model?
         */
        doGetEmailDetails:function (emailId, folderAlias) {

            // do we have full data for current email ? {
            var email = SKApp.user.simulation.mailClient.folders[folderAlias].getEmailByMySqlId(emailId);

            if ('undefined' === typeof email) {
                throw 'Try to render unexistent email ' + emailId + '.';
            }

            this.mailClient.setActiveEmail(email);

            // update active email in amails list {
            if (folderAlias === SKApp.user.simulation.mailClient.aliasFolderInbox) {
                this.updateInboxListView();
            }
            if (folderAlias === SKApp.user.simulation.mailClient.aliasFolderSended) {
                this.updateSendedListView();
            }
            if (folderAlias === SKApp.user.simulation.mailClient.aliasFolderDrats) {
                this.updateDratsListView();
            }
            if (folderAlias === SKApp.user.simulation.mailClient.aliasFolderTrash) {
                this.updateTrashListView();
            }
            // update active email in amails list }
            
            if ('undefined' !== typeof email.text &&
                'undefined' !== typeof email.attachment) {
                // if YES - just render it
                SKApp.user.simulation.mailClient.viewObject.renderEmaiPreviewScreen(
                    email,
                    SKApp.user.simulation.mailClient.viewObject.mailClientInboxFolderEmailPreviewId,
                    '120px'
                );
                    
                return;
            }
            // do we have full data for current email ? }

            // if NOT - send request to get copies string and attachment for current email
            SKApp.server.api(
                'mail/getMessage',
                {
                    emailId: emailId
                },
                function (response) {
                    if (1 === response.result) {
                        // update email {

                        var email = SKApp.user.simulation.mailClient.getEmailByMySqlId(response.data.id);
                        
                        // update attachment object
                        if (undefined !== response.data.attachments) {
                            var attachment = new SKAttachment();
                            attachment.id = response.data.attachments.id;
                            attachment.label = response.data.attachments.name;
                            email.attachment = attachment;
                        }
                        // update Copy to: string
                        email.copyToString = response.data.copies;

                        email.text = response.data.message;
                        
                        // update previouse email text - actual for re: and fwd:
                        if (undefined !== response.data.reply) {
                            email.previouseEmailText = response.data.reply;
                        }
                        // update email }

                        // render preview
                        SKApp.user.simulation.mailClient.viewObject.renderEmaiPreviewScreen(
                            email,
                            SKApp.user.simulation.mailClient.viewObject.mailClientInboxFolderEmailPreviewId,
                            '120px'
                        );

                    }
                });
        },

        /**
         */
        hideMailClientScreen:function () {
            $('#' + this.mailClientScreenID).hide();
        },

        updateFolderLabels:function () {
            var html = '';

            for (var alias in this.mailClient.folders) {
                if (this.mailClient.folders.hasOwnProperty(alias)) {
                    var isActiveCssClass = '';
                    if (this.mailClient.folders[alias].isActive) {
                        isActiveCssClass = ' active ';
                    }

                    var action = '';
                    if (alias === SKApp.user.simulation.mailClient.aliasFolderInbox) {
                        action = 'SKApp.user.simulation.mailClient.viewObject.doRenderFolder(SKApp.user.simulation.mailClient.aliasFolderInbox);';
                    }
                    if (alias === SKApp.user.simulation.mailClient.aliasFolderDrafts) {
                        action = 'SKApp.user.simulation.mailClient.viewObject.doRenderFolder(SKApp.user.simulation.mailClient.aliasFolderDrafts);';
                    }
                    if (alias === SKApp.user.simulation.mailClient.aliasFolderSended) {
                        action = 'SKApp.user.simulation.mailClient.viewObject.doRenderFolder(SKApp.user.simulation.mailClient.aliasFolderSended);';
                    }
                    if (alias === SKApp.user.simulation.mailClient.aliasFolderTrash) {
                        action = 'SKApp.user.simulation.mailClient.viewObject.doRenderFolder(SKApp.user.simulation.mailClient.aliasFolderTrash);';
                    }

                    html += _.template($('#MailClient_FolderLabel').html(), {
                        action:action,
                        label:this.mailClient.folders[alias].name,
                        isActiveCssClass:isActiveCssClass,
                        counter:this.mailClient.folders[alias].emails.length,
                        alias:alias
                    });
                }
            }

            $('#' + this.mailClientFoldersListId).html(html);
        },
        
        doRenderFolder: function(folderAlias) {
            if (this.mailClient.aliasFolderInbox === folderAlias) {
                this.mailClient.setActiveEmail(this.mailClient.getInboxFolder().getFirstEmail());
                this.renderInboxFolder();                
            }
            
            if (this.mailClient.aliasFolderSended === folderAlias) {
                this.mailClient.setActiveEmail(this.mailClient.getSendedFolder().getFirstEmail());
                this.renderSendedFolder();                
            }
            
            if (this.mailClient.aliasFolderDrafts === folderAlias) {
                this.mailClient.setActiveEmail(this.mailClient.getDraftsFolder().getFirstEmail());
                this.renderDraftsFolder();                
            }
            
            if (this.mailClient.aliasFolderTrash === folderAlias) {
                this.mailClient.setActiveEmail(this.mailClient.getTrashFolder().getFirstEmail());
                this.renderTrashFolder();                
            }
            
            this.mailClient.setActiveFolder(folderAlias);
            this.updateFolderLabels();
            this.mailClient.setActiveScreen(folderAlias);
        },

        updateInboxListView:function () {
            // generate emails list {
                       
            // We  use this 2 variables to separate emails to display unreaded emails first in list
            var readedEmailsList = '';
            var unreadedEmailsList = '';
            var incomingEmails = this.mailClient.folders[this.mailClient.aliasFolderInbox].emails; // to make code shorter

            for (var key in incomingEmails) {
                // check is email active
                var isActiveCssClass = '';
                if (incomingEmails[key].mySqlId == this.mailClient.activeEmail.mySqlId) {
                    // why 2 CSS classes? - this is works
                    isActiveCssClass = ' mail-emulator-received-list-string-selected active '; 
                } 
                
                // generate HTML by template
                var emailsList = _.template($('#MailClient_IncomeEmailLine').html(),{
                    
                    emailMySqlId:       incomingEmails[key].mySqlId,
                    senderName:         incomingEmails[key].senderNameString,
                    subject:            incomingEmails[key].subject.text,
                    sendedAt:           incomingEmails[key].sendedAt,
                    isHasAttachmentCss: incomingEmails[key].getIsHasAttachmentCss(),
                    isReadedCssClass:   incomingEmails[key].getIsReadedCssClass(),
                    isActiveCssClass:   isActiveCssClass
                }); 

                // Sort emails to display unreaded email first in list of emails {
                if (incomingEmails[key].isReaded()) {
                    readedEmailsList += emailsList;
                } else {
                    unreadedEmailsList += emailsList;
                }
                // Sort emails to display unreaded email first in list of emails }
            } 
            
            // add emails list
            $('#' + this.mailClientIncomeFolderListId + ' table').html('<tr class="email-list-separator"><td colspan="4">новые:</td></tr>' + unreadedEmailsList + '<tr class="email-list-separator"><td colspan="4">прочитанные:</td></tr>' + readedEmailsList);
            
            this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderInbox);
        },
        updateTrashListView:function () {
            // generate emails list {
                       
            // We  use this 2 variables to separate emails to display unreaded emails first in list
            var emailsList = '';
            var trashEmails = this.mailClient.folders[this.mailClient.aliasFolderTrash].emails; // to make code shorter

            for (var key in trashEmails) {
                // check is email active
                var isActiveCssClass = '';
                if (trashEmails[key].mySqlId == this.mailClient.activeEmail.mySqlId) {
                    // why 2 CSS classes? - this is works
                    isActiveCssClass = ' mail-emulator-received-list-string-selected active '; 
                } 
                
                // generate HTML by template
                emailsList += _.template($('#MailClient_TrashEmailLine').html(),{
                    
                    emailMySqlId:       trashEmails[key].mySqlId,
                    senderName:         trashEmails[key].senderNameString,
                    subject:            trashEmails[key].subject.text,
                    sendedAt:           trashEmails[key].sendedAt,
                    isHasAttachmentCss: trashEmails[key].getIsHasAttachmentCss(),
                    isReadedCssClass:   true,
                    isActiveCssClass:   isActiveCssClass
                }); 
            } 
            
            // add emails list
            $('#' + this.mailClientIncomeFolderListId + ' table').html(emailsList);
            
            this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderTrash);
        },

        updateSendedListView:function () {
            // generate emails list {
                       
            // We  use this 2 variables to separate emails to display unreaded emails first in list
            var emailsList = '';
            var sendedEmails = this.mailClient.folders[this.mailClient.aliasFolderSended].emails; // to make code shorter

            for (var key in sendedEmails) {
                // check is email active
                var isActiveCssClass = '';
                if (sendedEmails[key].mySqlId == this.mailClient.activeEmail.mySqlId) {
                    // why 2 CSS classes? - this is works
                    isActiveCssClass = ' mail-emulator-received-list-string-selected active '; 
                } 
                
                // generate HTML by template
                emailsList += _.template($('#MailClient_SendedEmailLine').html(),{
                    
                    emailMySqlId:       sendedEmails[key].mySqlId,
                    recipientName:      sendedEmails[key].getFormatedRecipientsString(),
                    subject:            sendedEmails[key].subject.text,
                    sendedAt:           sendedEmails[key].sendedAt,
                    isHasAttachmentCss: sendedEmails[key].getIsHasAttachmentCss(),
                    isReadedCssClass:   true,
                    isActiveCssClass:   isActiveCssClass
                }); 
            } 
            
            // add emails list
            $('#' + this.mailClientIncomeFolderListId + ' table').html(emailsList);
            
            this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderSended);
        },

        updateDraftsListView:function () {
            // generate emails list {
                       
            // We  use this 2 variables to separate emails to display unreaded emails first in list
            var emailsList = '';
            var draftEmails = this.mailClient.folders[this.mailClient.aliasFolderDrafts].emails; // to make code shorter

            for (var key in draftEmails) {
                // check is email active
                var isActiveCssClass = '';
                if (draftEmails[key].mySqlId == this.mailClient.activeEmail.mySqlId) {
                    // why 2 CSS classes? - this is works
                    isActiveCssClass = ' mail-emulator-received-list-string-selected active '; 
                } 
                
                // generate HTML by template
                emailsList += _.template($('#MailClient_SendedEmailLine').html(),{
                    
                    emailMySqlId:       draftEmails[key].mySqlId,
                    recipientName:      draftEmails[key].getFormatedRecipientsString(),
                    subject:            draftEmails[key].subject.text,
                    sendedAt:           draftEmails[key].sendedAt,
                    isHasAttachmentCss: draftEmails[key].getIsHasAttachmentCss(),
                    isReadedCssClass:   true,
                    isActiveCssClass:   isActiveCssClass
                }); 
            } 
            
            // add emails list
            $('#' + this.mailClientIncomeFolderListId + ' table').html(emailsList);
            
            this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderDrafts);
        },
        
        addClickAndDoubleClickBehaviour: function(folderAlias) {
            $('.email-list-line').click(function(event) {
                // if user click on same email line twise - open read email screen
                // Andrey, do not change == to ===
                if ($(event.currentTarget).data().emailId == SKApp.user.simulation.mailClient.activeEmail.mySqlId) {
                    SKApp.user.simulation.mailClient.renderReadEmailScreen(
                        $(event.currentTarget).data().emailId
                    );
                } else {
                    // if user clicks on different email lines - activate ckicked line email
                    SKApp.user.simulation.mailClient.viewObject.doGetEmailDetails(
                        $(event.currentTarget).data().emailId,
                        folderAlias
                    );
                }
            });
        },

        renderInboxFolder:function () {
            this.unhideFoldersBlock();
            
            // set HTML sceleton {
            var sceleton = _.template($('#MailClient_IncomeFolderSceleton').html(), {
                listId:         this.mailClientIncomeFolderListId,
                emailPreviewId: this.mailClientInboxFolderEmailPreviewId
            });

            $('#' + this.mailClientContentBlockId).html(sceleton);
            // set HTML sceleton }

            this.updateInboxListView();

            // render preview email
            if (undefined !== this.mailClient.activeEmail) {
                this.doGetEmailDetails(this.mailClient.activeEmail.mySqlId, this.mailClient.aliasFolderInbox);
            }

            this.renderIcons(this.mailClient.iconsForInboxScreenArray);
            this.mailClient.setActiveScreen(this.mailClient.screenInboxList);
        },

        renderTrashFolder:function () {
            this.unhideFoldersBlock();
            
            // set HTML sceleton {
            var sceleton = _.template($('#MailClient_TrashFolderSceleton').html(), {
                listId:this.mailClientIncomeFolderListId,
                emailPreviewId:this.mailClientInboxFolderEmailPreviewId
            });

            $('#' + this.mailClientContentBlockId).html(sceleton);
            // set HTML sceleton }

            this.updateTrashListView();

            // render preview email
            if (undefined !== this.mailClient.activeEmail) {
                this.doGetEmailDetails(this.mailClient.activeEmail.mySqlId, this.mailClient.aliasFolderTrash);
            }

            this.renderIcons(this.mailClient.iconsForTrashScreenArray);
            this.mailClient.setActiveScreen(this.mailClient.screenTrashList);
        },
        
        renderSendedFolder:function () {
            this.unhideFoldersBlock();
            
            // set HTML sceleton {
            var sceleton = _.template($('#MailClient_SendedFolderSceleton').html(), {
                listId:         this.mailClientIncomeFolderListId,
                emailPreviewId: this.mailClientInboxFolderEmailPreviewId
            });

            $('#' + this.mailClientContentBlockId).html(sceleton);
            // set HTML sceleton }
            
            this.updateSendedListView();
            
            // render preview email
            if (undefined !== this.mailClient.activeEmail) {
                this.doGetEmailDetails(this.mailClient.activeEmail.mySqlId, this.mailClient.aliasFolderSended);
            }
           
            this.renderIcons(this.mailClient.iconsForSendedScreenArray);
            
            // this dublicates model code, but this is first step to use models like data storage only
            
            this.updateFolderLabels();
            
             this.mailClient.setActiveScreen(this.mailClient.screenSendedList);
        },
        
        renderDraftsFolder:function () {
            this.unhideFoldersBlock();
            
            // set HTML sceleton {
            var sceleton = _.template($('#MailClient_SendedFolderSceleton').html(), {
                listId:         this.mailClientIncomeFolderListId,
                emailPreviewId: this.mailClientInboxFolderEmailPreviewId
            });

            $('#' + this.mailClientContentBlockId).html(sceleton);
            // set HTML sceleton }

            this.updateDraftsListView();

            // render preview email
            console.log('this.mailClient.activeEmail: ', this.mailClient.activeEmail);
            if (undefined !== this.mailClient.activeEmail) {
                this.doGetEmailDetails(this.mailClient.activeEmail.mySqlId, this.mailClient.aliasFolderDrafts);
            }
            
            this.renderIcons(this.mailClient.iconsForDraftsScreenArray);
            
            // this dublicates model code, but this is first step to use models like data storage only
            
            this.updateFolderLabels();
            
            this.mailClient.setActiveScreen(this.mailClient.screenDraftsList);
            
        },

        renderEmaiPreviewScreen:function (email, id, height) {
            this.mailClient.setActiveEmail(email);
            
            var attachmentLabel = '';
            if (undefined !== email.attachment) {
                attachmentLabel = email.attachment.label;
            }

            var emailPreviewTemplate = _.template($('#MailClient_EmailPreview').html(), {
                emailMySqlId:           email.mySqlId,
                senderName:             email.senderNameString,
                recipientName:          email.recipientNameString, //this.mailClient.heroNameEmail,
                copyNamesLine:          email.copyToString,
                subject:                email.subject.text,
                text:                   email.text,
                sendedAt:               email.sendedAt,
                isHasAttachmentCss:     email.getIsHasAttachmentCss(),
                isReadedCssClass:       email.getIsReadedCssClass(),
                attachmentFileName:     attachmentLabel,
                attachmentId:           email.attachment.id,
                height:                 height
            });

            $('#' + id).html(emailPreviewTemplate);
            
            this.renderPreviouseMessage(email.previouseEmailText);
            
            this.mailClient.setActiveScreen(this.mailClient.screenReadEmail);
        },

        renderReadEmail:function (email) {
            // set HTML sceleton {
            var sceleton = _.template($('#MailClient_ReadEmailSceleton').html(), {
                emailPreviewId:this.mailClientReadEmailContentBoxId
            });

            $('#' + this.mailClientContentBlockId).html(sceleton);
            // set HTML sceleton } 

            this.renderEmaiPreviewScreen(email, this.mailClientReadEmailContentBoxId, '350px');
        },

        renderIcons:function (iconButtonAliaces) {
            var me = this;
            // set defaults {
            var iconsListHtml = '';

            var addButtonNewEmail    = false;
            var addButtonReply       = false;
            var addButtonReplyAll    = false;
            var addButtonForward     = false;
            var addButtonAddToPlan   = false;
            var addButtonSend        = false;
            var addButtonSaveDraft   = false;
            var addButtonSendDraft   = false;
            var addButtonMoveToTrash = false;
            // set defaults }

            // choose icons to show {
            iconButtonAliaces.forEach(function (alias) {
                switch (alias) {
                    case me.mailClient.aliasButtonNewEmail:
                        addButtonNewEmail = true;
                        break;
                    case me.mailClient.aliasButtonReply:
                        addButtonReply = true;
                        break;
                    case me.mailClient.aliasButtonReplyAll:
                        addButtonReplyAll = true;
                        break;
                    case me.mailClient.aliasButtonForward:
                        addButtonForward = true;
                        break;
                    case me.mailClient.aliasButtonAddToPlan:
                        addButtonAddToPlan = true;
                        break;
                    case me.mailClient.aliasButtonSendDraft:
                        addButtonSendDraft = true;
                        break;
                    case me.mailClient.aliasButtonSend:
                        addButtonSend = true;
                        break;
                    case me.mailClient.aliasButtonSaveDraft:
                        addButtonSaveDraft = true;
                        break;
                    case me.mailClient.aliasButtonMoveToTrash:
                        addButtonMoveToTrash = true;
                        break;
                }
            });
            // choose icons to show }

            // conpose HTML code {
            // declarate action_icon just avoid long strings 
            var action_icon = $('#MailClient_ActionIcon').html();
            
            if (addButtonNewEmail) {
                iconsListHtml += _.template(action_icon, {
                    action:       'SKApp.user.simulation.mailClient.renderWriteCustomNewEmailScreen();',
                    iconCssClass: this.mailClient.aliasButtonNewEmail,
                    label:        'новое письмо'
                });
            }
            if (addButtonReply) {
                iconsListHtml += _.template(action_icon, {
                    action:       'SKApp.user.simulation.mailClient.viewObject.renderReplyToActiveEmailScreen();',
                    iconCssClass: this.mailClient.aliasButtonReply,
                    label:        'ответить'
                });
            }
            if (addButtonReplyAll) {
                iconsListHtml += _.template(action_icon, {
                    action:       'SKApp.user.simulation.mailClient.viewObject.renderReplyAllToActiveEmailScreen();',
                    iconCssClass: this.mailClient.aliasButtonReplyAll,
                    label:        'ответить всем'
                });
            }
            if (addButtonForward) {
                iconsListHtml += _.template(action_icon, {
                    action:       'SKApp.user.simulation.mailClient.viewObject.renderForwardActiveEmailScreen();',
                    iconCssClass: this.mailClient.aliasButtonForward,
                    label:        'переслать'
                });
            }
            if (addButtonAddToPlan) {
                iconsListHtml += _.template(action_icon, {
                    action:       '',
                    iconCssClass: this.mailClient.aliasButtonAddToPlan,
                    label:        'запланировать'
                });
            }
            if (addButtonSaveDraft) {
                iconsListHtml += _.template(action_icon, {
                    action:       'SKApp.user.simulation.mailClient.viewObject.doSaveCurrentCustomEmailToDrafts();',
                    iconCssClass: this.mailClient.aliasButtonSaveDraft,
                    label:        'сохранить'
                });
            }
            if (addButtonSend) {
                iconsListHtml += _.template(action_icon, {
                    action:       'SKApp.user.simulation.mailClient.viewObject.doSendCurrentCustomEmail();',
                    iconCssClass: this.mailClient.aliasButtonSend,
                    label:        'отправить'
                });
            }
            if (addButtonSendDraft) {
                iconsListHtml += _.template(action_icon, {
                    action:       'SKApp.user.simulation.mailClient.viewObject.doSendDraft();',
                    iconCssClass: this.mailClient.aliasButtonSendDraft,
                    label:        'отправить черновик'
                });
            }
            if (addButtonMoveToTrash) {
                iconsListHtml += _.template(action_icon, {
                    action:       'SKApp.user.simulation.mailClient.viewObject.doMoveToTrashActiveEmail();',
                    iconCssClass: this.mailClient.aliasButtonMoveToTrash,
                    label:        'удалить'
                });
            }
            // conpose HTML code }

            // render HTML
            $('#' + this.mailClientScreenID + ' .actions').html(iconsListHtml);
        },
        
        doMoveToTrashActiveEmail: function() {
            this.doMoveToTrash(SKApp.user.simulation.mailClient.activeEmail);
        },
        
        doMoveToTrash: function(email) {
            SKApp.server.api(
                'mail/move',
                {
                    folderId:  this.mailClient.codeFolderTrash,
                    messageId: email.mySqlId
                },
                function (){},
                false
            );
            
            this.mailClient.getInboxFolderEmails();
            this.mailClient.getTashFolderEmails();
            
            this.mailClient.setActiveEmail(undefined);
            var inboxEmails = this.mailClient.getInboxFolder().emails;
            for (var i in inboxEmails) {
                this.mailClient.setActiveEmail(inboxEmails[i]);
                break;
            }
            
            this.updateFolderLabels();
            this.renderInboxFolder();
        },
        
        hideFoldersBlock: function() {
            $("#" + this.mailClientScreenID + " header nav").hide();
            $("#" + this.mailClientContentBlockId).css('margin-left', '-180px');
        },
        
        unhideFoldersBlock: function() {            
            $("#" + this.mailClientContentBlockId).css('margin-left', '0px');
            $("#" + this.mailClientScreenID + " header nav").show();
        },
        
        renderWriteCustomNewEmailScreen: function() {
            
            // get template
            var htmlSceleton = _.template($("#MailClient_NewEmailScreen_Sceleton").html(), {});
            
            this.hideFoldersBlock();
            
            // render HTML sceleton
            $("#" + this.mailClientContentBlockId).html(htmlSceleton);
            
            this.renderIcons(this.mailClient.iconsForWriteEmailScreenArray);
            
            // add attachments list {
            this.mailClient.uploadAttachmentsList();

            var attachmentsListHtml = [];
            
            attachmentsListHtml.push({
                text:     "без вложения.",
                value:    0,
                selected: 1,
                imageSrc: ""
            });
            
            for (var i in this.mailClient.availableAttachments) {
                attachmentsListHtml.push({
                    text:     this.mailClient.availableAttachments[i].label,
                    value:    this.mailClient.availableAttachments[i].fileId,
                    imageSrc: this.mailClient.availableAttachments[i].getIconImagePath()
                });
            }
            
            $("#MailClient_NewLetterAttachment div.list").ddslick({
                data:          attachmentsListHtml,
                width:         '100%',
                selectText:    "Нет вложения.",
                imagePosition: "left"
            })
            // add attachments list }
            
            // bind recipients 
            $("#MailClient_RecipientsList").tagHandler({
                 availableTags: SKApp.user.simulation.mailClient.getFormatedCharacterList(),
                 autocomplete: true,
                 afterAdd : function(tag) { SKApp.user.simulation.mailClient.reloadSubjects(); },
                 afterDelete : function(tag) { SKApp.user.simulation.mailClient.reloadSubjects(); }
            });

            // bind copies
            $("#MailClient_CopiesList").tagHandler({
                availableTags: SKApp.user.simulation.mailClient.getFormatedCharacterList(),
                autocomplete: true
            });
            
            // bind subjects
            $("#MailClient_NewLetterSubject select").change(function() {
                SKApp.user.simulation.mailClient.reloadPhrases();
            });
        },
        
        getCurentEmailRecipientIds: function() {
            var list = [];
            var defaultRecipients = this.mailClient.defaultRecipients; // just to keep code shorter
            
            var valuesArray = $("#MailClient_RecipientsList li.tagItem").get();
            
            for (var i in valuesArray) {
                for (var j in defaultRecipients) {
                    // get IDs of character by label text comparsion
                    if ($(valuesArray[i]).text() === defaultRecipients[j].getFormatedForMailToName()) {
                        list.push(defaultRecipients[j].mySqlId);
                        break;
                    }
                }
            }
            
            return list;
        },
        
        getCurentEmailCopiesIds: function() {
            var list = [];
            var defaultRecipients = this.mailClient.defaultRecipients; // just to keep code shorter
            
            var valuesArray = $("#MailClient_CopiesList li").get();
            
            for (var i in valuesArray) {
                for (var j in defaultRecipients) {
                    // get IDs of character by label text comparsion
                    if ($(valuesArray[i]).text() === defaultRecipients[j].getFormatedForMailToName()) {
                        list.push(defaultRecipients[j].mySqlId);
                        break;
                    }
                }
            }
            
            return list;
        },
        
        updateSubjectsList: function() {
            var subjects = this.mailClient.availableSubjects; // to keep code shorter
            var listHtml = '<option value="0"></option>';
            
            for (var i in subjects) {
                listHtml += '<option value="' + subjects[i].characterSubjectId +'">' + subjects[i].getText() + '</option>';
            }
            
            $("#MailClient_NewLetterSubject select").html(listHtml);
        },
        
        /**
         * @param SKMailSubject subject
         */
        renderSingleSubject: function(subject) {
            var listHtml = '<option selected value="' 
                + subject.characterSubjectId +'">' + subject.getText() + '</option>';
            
            $("#MailClient_NewLetterSubject select").html(listHtml);
            $("#MailClient_NewLetterSubject select").attr("disabled", true);
        },
        
        getCurentEmailSubjectId: function() {
            // removeAttr - for reply, replyAll, forward cases
            $("#MailClient_NewLetterSubject select option:selected").removeAttr("disabled"); 
            
            return $("#MailClient_NewLetterSubject select option:selected").val();
        },
        
        getCurentEmailSubjectText: function() {
            return $("#MailClient_NewLetterSubject select option:selected").text();
        },
        
        reloadPhrases: function() {
            var phrases = this.mailClient.availablePhrases; 
            var addPhrases = this.mailClient.availableAdditionalPhrases;
            
            var mainPhrasesHtml = '';
            var additionalPhrasesHtml = '';
            
            for (var i in phrases) {
                mainPhrasesHtml += _.template($("#MailClient_PhraseItem").html(), {
                    phraseUid: phrases[i].uid,
                    phraseId:  phrases[i].mySqlId,
                    text:      phrases[i].text
                });
            }
            
            for (var i in addPhrases) {
                additionalPhrasesHtml += _.template($("#MailClient_PhraseItem").html(), {
                    phraseUid: addPhrases[i].uid,
                    phraseId:  addPhrases[i].mySqlId,
                    text:      addPhrases[i].text
                });
            }
            
            $("#mailEmulatorNewLetterTextVariants").html(mainPhrasesHtml);
            $("#mailEmulatorNewLetterTextVariantsAdd").html(additionalPhrasesHtml);
            
            $("#MailClient_ContentBlock .mail-tags-bl li").click(function() {
                var phrase = SKApp.user.simulation.mailClient.getAvailablePhraseByMySqlId($(this).data('id'));
                if (undefined === phrase) {
                    throw 'Undefined phrase id.';
                }
                // simplest way to clone small object in js {
                var phraseToAdd = new SKMailPhrase; // generate unique uid
                phraseToAdd.mySqlId = phrase.mySqlId;
                phraseToAdd.text    = phrase.text;
                // simplest way to clone small object in js }
                
                SKApp.user.simulation.mailClient.addPhraseToEmail(phraseToAdd);
            }); 
        },
        
        renderAddPhraseToEmail: function(phrase) {
            var phraseHtml = _.template($("#MailClient_PhraseItem").html(), {
                phraseUid: phrase.uid,
                phraseId:  phrase.mySqlId,
                text:      phrase.text
            });  
            
            $("#mailEmulatorNewLetterText").append(phraseHtml);
            
            $("#mailEmulatorNewLetterText li").click(function() {
                var phrase = SKApp.user.simulation.mailClient.getUsedPhraseByUid($(this).data('uid'));
                if (undefined === phrase) {
                    // if a have seweral (2,3,4...) phrases added to email - click handled twise
                    // currently I ignore this bug.
                    // @todo: fix it
                    throw 'Undefined phrase uid.';
                }
                SKApp.user.simulation.mailClient.removePhraseFromEmail(phrase);
            });
        },
        
        removePhraseFromEmail: function(phrase) {
            $("#mailEmulatorNewLetterText li[data-uid=" + phrase.uid + "]").remove();
        },
        
        /**
         * @return SKAttachment | undefined
         */
        getCurrentEmailAttachment: function() {
            var selectedAttachmentlabel = $('.dd-selected label').text();
            var attachments = this.mailClient.availableAttachments;
            
            if (undefined !== selectedAttachmentlabel && null !== selectedAttachmentlabel) {
                for (var i in attachments) {
                    if (selectedAttachmentlabel == attachments[i].label) {
                        return attachments[i];
                    }
                }
            }
            
            return undefined;
        },
        
        /**
         * @return integer | empty string
         */
        getCurrentEmailAttachmentFileId: function() {
            var file = this.getCurrentEmailAttachment();
            
            if (undefined === file) {
                return '';
            } else {
                return file.fileMySqlId;
            }
        },
        
        getCurrentEmailPhraseIds: function() {
            var list = [];
            
            var usedPhrases = $("#mailEmulatorNewLetterText li").get();
            
            for (var i in usedPhrases) {
                list.push($(usedPhrases[i]).data('id'));
            }
            
            return list;
        },
        
        generateNewEmailObject: function() {
            var emailToSave = new SKEmail();
            
            // recipients
            var recipients = this.getCurentEmailRecipientIds();
            emailToSave.recipients = []; // set empty realy nessesary
            for (var i in recipients) {
                emailToSave.recipients.push(this.mailClient.getCharacterById(recipients[i]));
            }
            
            // copies
            var copies = this.getCurentEmailCopiesIds();
            emailToSave.copyTo = []; // set empty realy nessesary
            for (var i in copies) {
                emailToSave.copyTo.push(this.mailClient.getCharacterById(copies[i]));
            }
            
            // subject
            var subject = new SKMailSubject();
            subject.characterSubjectId = this.getCurentEmailSubjectId();
            subject.text = this.getCurentEmailSubjectText();
            emailToSave.subject = subject;
            
            // attachment
            emailToSave.attachment = this.getCurrentEmailAttachment();
            
            // phrases
            var phrases = this.getCurrentEmailPhraseIds()
            for (var i in phrases) {
                emailToSave.phrases.push(this.mailClient.getAvailablePhraseByMySqlId(phrases[i]));
            }
            
            // update
            emailToSave.updateStatusPropertiesAccordingObjects();
            
            return emailToSave;
        },
        
        doSaveCurrentCustomEmailToDrafts: function() {
            var emailToSave = this.generateNewEmailObject();
            
            this.mailClient.saveToDraftsNewCustomEmail(emailToSave); // sync AJAX
            
            this.updateFolderLabels();
            this.renderInboxFolder();
        },
        
        doSendCurrentCustomEmail: function() {
            var emailToSave = this.generateNewEmailObject();
            
            this.mailClient.sendNewCustomEmail(emailToSave); // sync AJAX
            
            this.updateFolderLabels();
            this.renderInboxFolder();
        },
        
        renderWriteEmailScreen: function(iconsList) {
            if (0 == this.mailClient.defaultRecipients.length) {
                this.mailClient.updateRecipientsList();
            }
            
            // get template
            var htmlSceleton = _.template($("#MailClient_NewEmailScreen_Sceleton").html(), {});
            
            this.hideFoldersBlock();
            
            // render HTML sceleton
            $("#" + this.mailClientContentBlockId).html(htmlSceleton);
            
            this.renderIcons(this.mailClient.iconsForWriteEmailScreenArray);
            
            // add attachments list {
            this.mailClient.uploadAttachmentsList();

            var attachmentsListHtml = [];
            
            attachmentsListHtml.push({
                text:     "без вложения.",
                value:    0,
                selected: 1,
                imageSrc: ""
            });
            
            for (var i in this.mailClient.availableAttachments) {
                attachmentsListHtml.push({
                    text:     this.mailClient.availableAttachments[i].label,
                    value:    this.mailClient.availableAttachments[i].fileId,
                    imageSrc: this.mailClient.availableAttachments[i].getIconImagePath()
                });
            }
            
            $("#MailClient_NewLetterAttachment div.list").ddslick({
                data:          attachmentsListHtml,
                width:         '100%',
                selectText:    "Нет вложения.",
                imagePosition: "left"
            })
            // add attachments list }
            
            // bind recipients 
            // realized in custom way
            
            // bind subjects
            $("#MailClient_NewLetterSubject select").change(function() {
                SKApp.user.simulation.mailClient.reloadPhrases();
            });
        },

        renderPreviouseMessage: function(text) {
            if (undefined !== text && '' !== text) {
                text = '<pre><p style="color:blue;">' + text + '</p></pre>';
            }
            $(".previouse-message-text").html(text);
        },
        
        /**
         * @param mixed array response, API response
         */
        doUpdateScreenFromReplyEmailData: function(response) {
            if (1 == response.result) {
                var subject = new SKMailSubject();
                subject.text               = response.subject;
                subject.mySqlId            = response.subjectId;
                subject.characterSubjectId = response.subjectId;

                SKApp.user.simulation.mailClient.viewObject.renderSingleSubject(subject);

                SKApp.user.simulation.mailClient.viewObject
                    .renderPreviouseMessage(response.phrases.previouseMessage);

                // even if there is one recipient,but it must be an array
                var recipients = [SKApp.user.simulation.mailClient.getRecipientByMySqlId(response.receiver_id)
                        .getFormatedForMailToName()];
                
                // set recipients 
                $("#MailClient_RecipientsList").tagHandler({
                     assignedTags:  recipients,
                     availableTags: recipients,
                     allowAdd:      false,
                     allowEdit:     false
                });
                
                // add copies if they exests {
                var copies = [];
                if (undefined !== response.copiesIds) {
                    var ids = response.copiesIds.split(',');
                    for (var i in ids) {
                        if (0 < parseInt(ids[i])) {
                            copies.push(SKApp.user.simulation.mailClient.getRecipientByMySqlId(parseInt(ids[i]))
                                .getFormatedForMailToName());
                        }
                    }                    
                }
                
                $("#MailClient_CopiesList").tagHandler({
                    assignedTags:  copies,
                    availableTags: SKApp.user.simulation.mailClient.getFormatedCharacterList(),
                    autocomplete: true
                });
                // add copies if they exests }

                // add phrases {
                SKApp.user.simulation.mailClient
                    .setRegularAvailablePhrases(response.phrases.data);

                SKApp.user.simulation.mailClient
                    .setAdditionalAvailablePhrases(response.phrases.addData);

                SKApp.user.simulation.mailClient.viewObject.reloadPhrases();
                // add phrases }
            } else {
                throw "Can`t initialize responce email.";
            } 
        },
        
        /**
         * @param mixed array response, API response
         */
        doUpdateScreenFromForwardEmailData: function(response) {
            if (1 == response.result) {
                var subject = new SKMailSubject();
                subject.text               = response.subject;
                subject.mySqlId            = response.subjectId;
                subject.characterSubjectId = response.subjectId;

                SKApp.user.simulation.mailClient.viewObject.renderSingleSubject(subject);

                SKApp.user.simulation.mailClient.viewObject
                    .renderPreviouseMessage(response.phrases.previouseMessage);

                // set recipients 
                $("#MailClient_RecipientsList").tagHandler({
                     availableTags: SKApp.user.simulation.mailClient.getFormatedCharacterList(),
                     autocomplete: true
                });
                
                $("#MailClient_CopiesList").tagHandler({
                    availableTags: SKApp.user.simulation.mailClient.getFormatedCharacterList(),
                    autocomplete: true
                });
                // add copies if they exests }

                // add phrases {
                SKApp.user.simulation.mailClient
                    .setRegularAvailablePhrases(response.phrases.data);

                SKApp.user.simulation.mailClient
                    .setAdditionalAvailablePhrases(response.phrases.addData);

                SKApp.user.simulation.mailClient.viewObject.reloadPhrases();
                // add phrases }
            } else {
                throw "Can`t initialize responce email.";
            } 
        },        
        
        renderReplyToActiveEmailScreen: function() {
            this.renderWriteEmailScreen(this.mailClient.iconsForWriteEmailScreenArray);
            
            SKApp.server.api(
                'mail/reply',
                {
                    id: this.mailClient.activeEmail.mySqlId
                }, 
                function (response) {
                    if (1 == response.result) {
                        SKApp.user.simulation.mailClient.viewObject.doUpdateScreenFromReplyEmailData(response);
                    } else {
                        throw "Can`t initialize responce email.";
                    }
                },
                false
            ); 
                
            this.mailClient.setActiveScreen(this.mailClient.screenWriteReply);
        },
        
        renderReplyAllToActiveEmailScreen: function() {
            this.renderWriteEmailScreen(this.mailClient.iconsForWriteEmailScreenArray);
            
            SKApp.server.api(
                'mail/replyAll',
                {
                    id: this.mailClient.activeEmail.mySqlId
                }, 
                function (response) {
                    if (1 == response.result) {
                        SKApp.user.simulation.mailClient.viewObject.doUpdateScreenFromReplyEmailData(response);
                    } else {
                        throw "Can`t initialize responce email.";
                    }
                },
                false
            );
                
            this.mailClient.setActiveScreen(this.mailClient.screenWriteReplyAll);
        },
        
        renderForwardActiveEmailScreen: function() {
            this.renderWriteEmailScreen(this.mailClient.iconsForWriteEmailScreenArray);
            
            SKApp.server.api(
                'mail/forward',
                {
                    id: this.mailClient.activeEmail.mySqlId
                }, 
                function (response) {
                    if (1 == response.result) {
                        SKApp.user.simulation.mailClient.viewObject.doUpdateScreenFromForwardEmailData(response);
                    } else {
                        throw "Can`t initialize responce email.";
                    }
                },
                false
            ); 
                
            this.mailClient.setActiveScreen(this.mailClient.screenWriteForward);
        },
        
        doSendDraft: function() {
            SKApp.server.api(
                'mail/sendDraft',
                {
                    id: this.mailClient.activeEmail.mySqlId
                }, 
                function (response) {
                    if (1 != response.result ) {
                    // display message for user
                        SKApp.user.simulation.mailClient.message_window =
                            SKApp.user.simulation.mailClient.message_window || new SKDialogView({
                            'message': 'Не удалось отправить черновик адресату.',
                            'buttons': [
                                {
                                    'value': 'Окей',
                                    'onclick': function () {
                                        delete SKApp.user.simulation.mailClient.message_window;
                                    }
                                }
                            ]
                        });
                    }
                },
                false
            ); 

            this.mailClient.getDraftsFolderEmails();
            this.mailClient.getSendedFolderEmails();
            
            // get first email if email exist in folder {
            var draftEmails = this.mailClient.getDraftsFolder().emails;
            
            SKApp.user.simulation.mailClient.activeEmail = undefined;
            for (var i in draftEmails) {
                SKApp.user.simulation.mailClient.activeEmail = draftEmails[i];
            }
            // get first email if email exist in folder }
 
            this.renderDraftsFolder();
        }
    });
})();