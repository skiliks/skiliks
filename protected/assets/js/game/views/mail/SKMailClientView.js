/*global Backbone, _, SKApp, SKAttachment */
(function () {
    "use strict";
    window.SKMailClientView = Backbone.View.extend({

        mailClientScreenID:'mailEmulatorMainScreen',

        mailClientFoldersListId:'MailClient_FolderLabels',

        mailClientContentBlockId:'MailClient_ContentBlock',

        mailClientIncomeFolderListId:'MailClient_IncomeFolder_List',

        mailClientIncomeFolderEmailPreviewId:'MailClient_IncomeFolder_EmailPreview',

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
        doGetEmailDetails:function (emailId) {
            // do we have full data for current email ? {
            var email = SKApp.user.simulation.mailClient.getIncomeFolder().getEmailByMySqlId(emailId);

            if ('undefined' === typeof email) {
                throw 'Try to render unexistent email ' + emailId + '.';
            }

            this.mailClient.setActiveEmail(email);

            if ('undefined' !== typeof email.text &&
                'undefined' !== typeof email.attachment) {
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
                {
                    emailId:emailId
                },
                function (response) {
                    if (1 === response.result) {
                        // update email {
                        var email = SKApp.user.simulation.mailClient.getIncomeFolder()
                            .getEmailByMySqlId(response.data.id);

                        // update attachment object
                        var attachment = new SKAttachment();
                        attachment.id = response.data.attachments.id;
                        attachment.label = response.data.attachments.name;
                        email.attachment = attachment;

                        // update Copy to: string
                        email.copyToString = response.data.copies;

                        email.text = response.data.message;
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
                    if (alias === SKApp.user.simulation.mailClient.aliasFolderIncome) {
                        action = 'SKApp.user.simulation.mailClient.preRenderFolder(SKApp.user.simulation.mailClient.aliasFolderIncome);';
                    }
                    if (alias === SKApp.user.simulation.mailClient.aliasFolderDrafts) {
                        action = '';
                    }
                    if (alias === SKApp.user.simulation.mailClient.aliasFolderSended) {
                        action = '';
                    }
                    if (alias === SKApp.user.simulation.mailClient.aliasFolderTrash) {
                        action = '';
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

        updateIncomeListView:function () {
            // generate emails list {
                       
            // We  use this 2 variables to separate emails to display unreaded emails first in list
            var readedEmailsList = '';
            var unreadedEmailsList = '';
            var incomingEmails = this.mailClient.folders[this.mailClient.aliasFolderIncome].emails; // to make code shorter

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
            $('#' + this.mailClientIncomeFolderListId + ' table').html(unreadedEmailsList + readedEmailsList);
            
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
                        $(event.currentTarget).data().emailId
                    );
                }
            });
        },

        renderIncomeFolder:function () {
            // set HTML sceleton {
            var sceleton = _.template($('#MailClient_IncomeFolderSceleton').html(), {
                listId:this.mailClientIncomeFolderListId,
                emailPreviewId:this.mailClientIncomeFolderEmailPreviewId
            });

            $('#' + this.mailClientContentBlockId).html(sceleton);
            // set HTML sceleton }

            this.updateIncomeListView();

            // render preview email
            if (undefined !== this.mailClient.activeEmail) {
                this.doGetEmailDetails(this.mailClient.activeEmail.mySqlId);
            }

            this.renderIcons(this.mailClient.iconsForIncomeScreenArray);
        },

        renderEmaiPreviewScreen:function (email, id) {
            this.updateIncomeListView();

            var emailPreviewTemplate = _.template($('#MailClient_EmailPreview').html(), {
                emailMySqlId:email.mySqlId,
                senderName:email.senderNameString,
                recipientName:this.mailClient.heroNameEmail,
                copyNamesLine:email.copyToString,
                subject:email.subject.text,
                text:email.text,
                sendedAt:email.sendedAt,
                isHasAttachmentCss:email.getIsHasAttachmentCss(),
                isReadedCssClass:email.getIsReadedCssClass(),
                attachmentFileName:email.attachment.label,
                attachmentId:email.attachment.id
            });

            $('#' + id).html(emailPreviewTemplate);
        },

        renderReadEmail:function (email) {
            // set HTML sceleton {
            var sceleton = _.template($('#MailClient_ReadEmailSceleton').html(), {
                emailPreviewId:this.mailClientReadEmailContentBoxId
            });

            $('#' + this.mailClientContentBlockId).html(sceleton);
            // set HTML sceleton } 

            this.renderEmaiPreviewScreen(email, this.mailClientReadEmailContentBoxId);
        },

        renderIcons:function (iconButtonAliaces) {
            var me = this;
            // set defaults {
            var iconsListHtml = '';

            var addButtonNewEmail  = false;
            var addButtonReply     = false;
            var addButtonReplyAll  = false;
            var addButtonForward   = false;
            var addButtonAddToPlan = false;
            var addButtonSend      = false;
            var addButtonSave      = false;
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
                    case me.mailClient.aliasButtonSaveDraft:
                        addButtonSave = true;
                        break;
                    case me.mailClient.aliasButtonSend:
                        addButtonSend = true;
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
                    action:       '',
                    iconCssClass: this.mailClient.aliasButtonReply,
                    label:        'ответить'
                });
            }
            if (addButtonReplyAll) {
                iconsListHtml += _.template(action_icon, {
                    action:       '',
                    iconCssClass: this.mailClient.aliasButtonReplyAll,
                    label:        'ответить всем'
                });
            }
            if (addButtonForward) {
                iconsListHtml += _.template(action_icon, {
                    action:       '',
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
            if (addButtonAddToPlan) {
                iconsListHtml += _.template(action_icon, {
                    action:       '',
                    iconCssClass: this.mailClient.aliasButtonAddToPlan,
                    label:        'запланировать'
                });
            }
            if (addButtonSave) {
                iconsListHtml += _.template(action_icon, {
                    action:       '',
                    iconCssClass: this.mailClient.aliasButtonSaveDraft,
                    label:        'сохранить'
                });
            }
            if (addButtonSend) {
                iconsListHtml += _.template(action_icon, {
                    action:       '',
                    iconCssClass: this.mailClient.aliasButtonSend,
                    label:        'отправить'
                });
            }
            // conpose HTML code }

            // render HTML
            $('#' + this.mailClientScreenID + ' .actions').html(iconsListHtml);
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
            
//            var attachmentsListHtml = _.template($("#MailClient_AttachmentOptionItem").html(), {
//                fileId: 0,
//                label:  '',
//                iconFile: 'ppt.png'
//            }); 

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
//                attachmentsListHtml += _.template($("#MailClient_AttachmentOptionItem").html(), {
//                    fileId:   this.mailClient.availableAttachments[i].fileId,
//                    label:    this.mailClient.availableAttachments[i].label,
//                    iconFile: 'ppt.png'
//                });
            }
            
            $("#MailClient_NewLetterAttachment div.list").ddslick({
                data:          attachmentsListHtml,
                width:         '100%',
                selectText:    "Нет вложения.",
                imagePosition: "left",
                onSelected:   function(selectedData){
                    //callback function: do something with selectedData;
                }   
            })
            
            // $("#MailClient_NewLetterAttachment").html(attachmentsListHtml);
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
            
            var valuesArray = $("#MailClient_RecipientsList li").get();
            
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
        
        getCurentEmailSubjectId: function() {
            return $("#MailClient_NewLetterSubject select option:selected").val();
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
            
            $("#mailEmulatorNewLetterText ul li").click(function() {
                var phrase = SKApp.user.simulation.mailClient.getUsedPhraseByUid($(this).data('uid'));
                if (undefined === phrase) {
                    // if a have seweral (2,3,4...) phrases added to email - click handled twise
                    // currently I ignore this bug.
                    // @todo: fix it
                    throw 'Undefined phrase uid.';
                }
                SKApp.user.simulation.mailClient.removePhraseFromEmail(phrase, $(this).eq());
            });
        },
        
        removePhraseFromEmail: function(phrase) {
            $("#mailEmulatorNewLetterText li[data-uid=" + phrase.uid + "]").remove();
        }
    });
})();