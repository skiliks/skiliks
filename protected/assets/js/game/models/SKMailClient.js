/*global Backbone, SKMailClientView, SKMailFolder, SKMailSubject, SKEmail, SKApp, SKDialogView*/
(function() {
    "use strict";
    window.SKMailClient = Backbone.Model.extend({
        
        // --------------------------------------------------
        
        // @var string

        aliasFolderInbox : 'INBOX',

        // @var string
        aliasFolderDrafts: 'DRAFTS',

        // @var string
        aliasFolderSended: 'SENDED',

        // @var string
        aliasFolderTrash: 'TRASH',
        
        // @var string
        codeFolderInbox : 1,

        // @var string
        codeFolderDrafts: 2,

        // @var string
        codeFolderSended: 3,

        // @var string
        codeFolderTrash: 4,
        
        // --------------------------------------------------
        
        emailTypeNew: 'new',
        
        // --------------------------------------------------

        // @var string
        aliasButtonNewEmail:    'NEW_EMAIL',

        // @var string
        aliasButtonReply:       'REPLY_EMAIL',

        // @var string
        aliasButtonReplyAll:    'REPLY_ALL_EMAIL',

        // @var string
        aliasButtonForward:     'FORWARD_EMAIL',

        // @var string
        aliasButtonSend:        'SEND_EMAIL',

        // @var string
        aliasButtonSendDraft:   'SEND_DRAFT_EMAIL',

        // @var string
        aliasButtonSaveDraft:   'SAVE_TO_DRAFTS',
        
        // @var string
        aliasButtonAddToPlan:   'ADD_TO_PLAN',
        
        // @var string
        aliasButtonMoveToTrash: 'MOVE_TO_TRASH',
        
        // unfortunatey this checnge context inside new Array, so I need to use literals
        iconsForInboxScreenArray: [
            'NEW_EMAIL',
            'REPLY_EMAIL',
            'REPLY_ALL_EMAIL',
            'FORWARD_EMAIL',
            'ADD_TO_PLAN',
            'MOVE_TO_TRASH'
        ],
        iconsForTrashScreenArray: [
            'NEW_EMAIL',
            'REPLY_EMAIL',
            'REPLY_ALL_EMAIL',
            'FORWARD_EMAIL',
            'ADD_TO_PLAN'
        ],
            
        iconsForWriteEmailScreenArray: [
            'SEND_EMAIL',
            'SAVE_TO_DRAFTS'
        ],
        
        iconsForDraftsScreenArray: [
            'NEW_EMAIL',
            'SEND_DRAFT_EMAIL'
        ],
        
        iconsForSendedScreenArray: [
            'NEW_EMAIL'
        ],
        
        // --------------------------------------------------
        
        // @var string
        screenInboxList: 'SCREEN_INBOX_LIST',
        
        // @var string
        screenReadEmail: 'SCREEN_READ_EMAIL',

        // @var string
        screenDraftsList: 'SCREEN_DRAFTS_LIST',

        // @var string
        screenSendedList: 'SCREEN_SENDED_LIST',

        // @var string
        screenTrashList: 'SCREEN_TRASH_LIST',

        // @var string
        screenWriteNewCustomEmail: 'SCREEN_WRITE_NEW_EMAIL',

        // @var string
        screenWriteReply: 'SCREEN_WRITE_REPLY',

        // @var string
        screenWriteDraft: 'SCREEN_WRITE_DRAFT',
        
        // --------------------------------------------------
 
        // @var string
        heroName: 'Федоров А.В.',
        
        // --------------------------------------------------
        
        // @var stringone of 'screenXXX' literals
        currentScreen: undefined,
        
        // @var SkWindow
        windowObject: undefined,
        
        // @var SKMailClientView
        viewObject: new SKMailClientView(),
        
        // @var SKEmail
        activeEmail: undefined,
        
        // @var string
        activeScreen: undefined,
        
        // @var array of SkMailFolder
        folders: [],
        
        // @var array of SkCharacter
        defaultRecipients: [],
        
        // use just to store avaliable for current new email subjects according recipients list
        // refreshed for each new email and each recipients list change
        // @var array of 
        availableSubjects: [],
        
        // @var array of SKMailAttachment
        availableAttachments: [],
        
        // @var array of SKMailPhrase
        availablePhrases: [],
        
        // @var array of SKMailPhrase
        // this is ',', '.', ':' etc. - symbols for any phrases set
        availableAdditionalPhrases: [],
        
        // @var array of SKMailPhrase
        newEmailUsedPhrases: [],
        
        // @var array of SKMailAttachment
        newEmailAttachment: undefined,
        
        // @var undefined | SKEmail
        lastNotSavedEmail: undefined,
        
        // -------------------------------------------------
        
        initialize: function() {
            this.folders[this.aliasFolderInbox]  = new SKMailFolder();
            this.folders[this.aliasFolderDrafts] = new SKMailFolder();
            this.folders[this.aliasFolderSended] = new SKMailFolder();
            this.folders[this.aliasFolderTrash]  = new SKMailFolder();

            this.viewObject.setMailClient(this);
        },
        
        /**
         * @return SkMailFolder
         */
        getInboxFolder: function(){
            return this.folders[this.aliasFolderInbox];
        },
        
        /**
         * @return SkMailFolder
         */

        getDraftsFolder: function(){
            return this.folders[this.aliasFolderDrafts];
        },
        
        /**
         * @return SkMailFolder
         */

        getSendedFolder: function(){
            return this.folders[this.aliasFolderSended];
        },
        
        /**
         * @return SkMailFolder
         */
        getTrashFolder: function(){
            return this.folders[this.aliasFolderTrash];
        },
        
        /**
         * Init folder names by server data
         * data = array {
         *   1 => array(
         *     name => '', id => 1, unreaded => 0,
         *   ),
         *   2 => ...
         * }
         */
        updateFolders: function(data) {
            this.getInboxFolder().name = data[this.codeFolderInbox].name;
            this.getDraftsFolder().name = data[this.codeFolderDrafts].name;
            this.getSendedFolder().name = data[this.codeFolderSended].name;
            this.getTrashFolder().name  = data[this.codeFolderTrash].name;
        },
        
        getFolderAliasById: function(folderId) {
            folderId = parseInt(folderId, 10);
            if (1 === folderId) {
                return this.aliasFolderInbox;
            }  
            if (2 === folderId) {
                return this.aliasFolderSended;
            }  
            if (3 === folderId) {
                return this.aliasFolderDrafts;
            }  
            if (4 === folderId) {
                return this.aliasFolderTrash;
            }  
        },
        
        /**
         * CleanUp 'folderAlias' folder
         * Push all emails from 'emails' to 'folderAlias' folder
         *
         * @param folderAlias
         * @param emailsData
         */
        setEmailsToFolder: function(folderAlias, emailsData) { 
            this.folders[folderAlias].emails = [];
            
            for (var id in emailsData) {
                if (emailsData.hasOwnProperty(id)) {
                    var subject = new SKMailSubject();
                    subject.text = emailsData[id].subject;

                    var email = new SKEmail();
                    email.mySqlId             = emailsData[id].id;
                    email.is_readed           = (1 === parseInt(emailsData[id].readed, 10));
                    email.is_has_attachment   = (1 === parseInt(emailsData[id].attachments, 10));
                    email.sendedAt            = emailsData[id].receivingDate;
                    email.subject             = subject;
                    email.senderNameString    = emailsData[id].sender.replace('<','(').replace('>',')');
                    email.recipientNameString = emailsData[id].receiver.replace('<','(').replace('>',')');

                    this.folders[folderAlias].emails.push(email);
                }
            }
        },
        
        /**
         * @param string activeScreenAlias, this.screenXxx literals
         */
        setActiveScreen: function(activeScreenAlias) {
            this.activeScreen = activeScreenAlias;  
        },
        
        // ----------------------------------------------
        
        /**
         * @param integer emailId
         * @param string folderAlias
         */
        'moveEmailToFolder': function(emailId, folderAlias) {
            
        },
        
        'saveDraft': function() {
            
        },
        
        'sendDraft': function() {
            
        },
        
        'sendEmail': function() {
            
        },
        
        'updateSubjectList': function() {
            
        },
        
        'updateAttachmentsList': function() {
            
        },
        
        // ---------------------------------------------
        
        /**
         * This is just alias to make initial screen flexible
         */

        getDataForInitialScreen: function() {
            SKApp.server.api(
                'mail/getFolders',
                {},
                function (data) {
                    SKApp.user.simulation.mailClient.renderInitialScreen(data.folders, data.messages);
                });
        },
        
        // todo: combine all getXxxFolderEmails() to one method.
        getInboxFolderEmails: function() {
            SKApp.server.api(
                'mail/getMessages',
                {
                    folderId:   SKApp.user.simulation.mailClient.codeFolderInbox,
                    order:      1,
                    order_type: 0
                },
                function (responce) {
                    SKApp.user.simulation.mailClient.updateInboxFolderEmails(responce.messages);
                },
                false
            );
        },
        
        getDraftsFolderEmails: function() {
            SKApp.server.api(
                'mail/getMessages',
                {
                    folderId:   SKApp.user.simulation.mailClient.codeFolderDrafts,
                    order:      2,
                    order_type: 0
                },
                function (responce) {
                    SKApp.user.simulation.mailClient.updateDraftsFolderEmails(responce.messages);
                },
                false
            );
        },        
        
        getSendedFolderEmails: function() {
            SKApp.server.api(
                'mail/getMessages',
                {
                    folderId:   SKApp.user.simulation.mailClient.codeFolderSended,
                    order:      -1,
                    order_type: 0
                },
                function (responce) {
                    SKApp.user.simulation.mailClient.updateSendedFolderEmails(responce.messages);
                },
                false
            );
        },
        
        getTashFolderEmails: function() {
            SKApp.server.api(
                'mail/getMessages',
                {
                    folderId:   SKApp.user.simulation.mailClient.codeFolderTrash,
                    order:      -1,
                    order_type: 0
                },
                function (responce) {
                    SKApp.user.simulation.mailClient.updateTrashFolderEmails(responce.messages);
                },
                false
            );
        },
        
        // todo: combine all updateXxxFolderEmails() to one method.
        updateInboxFolderEmails: function(messages) {
            this.setEmailsToFolder(this.aliasFolderInbox, messages);
        },
        
        updateDraftsFolderEmails: function(messages) {
            this.setEmailsToFolder(this.aliasFolderDrafts, messages);
        },
        
        updateSendedFolderEmails: function(messages) {
            this.setEmailsToFolder(this.aliasFolderSended, messages);
        },
        
        updateTrashFolderEmails: function(messages) {
            this.setEmailsToFolder(this.aliasFolderTrash, messages);
        },
        
        setActiveEmailFromArray: function(emails) {
            if (0 === emails.length) {
                this.activeEmail = undefined;
                return false;
            }

            for (var key in emails) {
                this.activeEmail = emails[key];
                return true;
            }            
         },
        
        setActiveEmail: function(email) {
            // active email or readed or new writed in any case
            if (undefined !== email) {
                email.is_readed = true;
            }
            
            this.activeEmail = email;
         },
        
        setActiveFolder: function(alias) {
            for (var i in this.folders) {
                this.folders[i].isActive = false;
                if (alias === i) {
                    this.folders[i].isActive = true;
                }
            }
         },
         
         getActiveFolder: function() {
            for (var i in this.folders) {
                if (this.folders[i].isActive === true) {
                    return this.folders[i].isActive;
                }
            }    
         },
        
         renderInitialScreen: function(folders, messages) {
            // process and store in model AJAX data {
            this.updateFolders(folders);
            this.setEmailsToFolder(this.aliasFolderInbox, messages);
            // process and store in model AJAX data }
            
            // mark INCOM foldes as active
            this.folders[this.aliasFolderInbox].isActive = true;
            
            // set as active first letter in Inbox folder {
            var emails = this.folders[this.aliasFolderInbox].emails;
            if (0 < emails.length) {
                for (var key in emails) {
                    if (emails.hasOwnProperty(key)) {
                        this.setActiveEmail(emails[key]);
                        break;
                    }
                }
            }
            // set as active first letter in Inbox folder }
            
            this.viewObject.renderMailClientScreenBase();
            this.viewObject.doRenderFolder(this.aliasFolderInbox);
        },
        
        /**
         * Returns email bi it`s id from anu folder.
         * By the way, any email stored in single folder in any moment of time
         */
        getEmailByMySqlId: function(emailId) {
            for (var alias in this.folders) {
                if (this.folders.hasOwnProperty(alias)){
                    var emails = this.folders[alias].emails;
                    for (var i in emails) {
                        if (emails.hasOwnProperty(i)) {
                            // Andrey, do not change == to ===
                            if (emails[i].mySqlId == emailId) {
                                return emails[i];
                            }
                        }
                    }
                }
            }
        },
        
        renderReadEmailScreen: function(emailId) {
            this.viewObject.renderReadEmail(this.getEmailByMySqlId(emailId));
            
            this.setActiveScreen(this.screenReadEmail);
        },
        
        saveAttachmentToMyDocuments: function(attachmentId) {
            // call saveAttachment URL
            SKApp.server.api(
                'myDocuments/add',
                { 
                    attachmentId: attachmentId
                }, 
                function (response) {
                    // and display message for user
                    SKApp.user.simulation.mailClient.message_window =
                        SKApp.user.simulation.mailClient.message_window || new SKDialogView({
                        'message': 'Файл был успешно сохранён в папку Мои документы.',
                        'buttons': [
                            {
                                'value': 'Окей',
                                'onclick': function () {
                                    delete SKApp.user.simulation.mailClient.message_window;
                                }
                            }
                        ]
                    });
                });   
        },
        
        updateRecipientsList: function() {
            SKApp.server.api(
                'mail/getReceivers',
                {}, 
                function (response) {
                    if (undefined !== response.data) {
                        for (var i in response.data) {
                            var string = response.data[i];
                            
                            var character = new SKCharacter();
                            character.mySqlId = i;
                            character.excelId = i;
                            character.name    = $.trim(string.substr(0, string.indexOf('<')));
                            character.email   = $.trim(string.substr(string.indexOf('<'), string.length));
                            character.email   = character.email.replace('<', '');
                            character.email   = character.email.replace('>', '');
                            
                            SKApp.user.simulation.mailClient.defaultRecipients.push(character);
                        }
                    }
                },
                false
            ); 
        },        
        
        getFormatedCharacterList: function() {
            var list = [];
            for (var i in this.defaultRecipients) {
                // non strict "!=" is important!
                if ('' != this.defaultRecipients[i].name && '' != this.defaultRecipients[i].email) {
                    list.push(this.defaultRecipients[i].getFormatedForMailToName());
                }
            }
            
            return list;
        },
        
        renderWriteCustomNewEmailScreen: function() {
            if (0 == this.defaultRecipients.length) {
                this.updateRecipientsList();
            }
            
            this.viewObject.renderWriteCustomNewEmailScreen();
        },
        
        reloadSubjects: function() {
            var recipientIds = this.viewObject.getCurentEmailRecipientIds();
            
            SKApp.server.api(
                'mail/getThemes',
                {
                    receivers: recipientIds.join(',') // implode()
                }, 
                function (response) {
                    if (undefined !== response.data) {
                        for (var i in response.data) {
                            var string = response.data[i];
                            
                            var subject = new SKMailSubject();
                            subject.characterSubjectId = i;
                            subject.text = response.data[i];
                            
                            SKApp.user.simulation.mailClient.availableSubjects.push(subject);
                        }
                    }
                },
                false
            ); 
                
            this.viewObject.updateSubjectsList();
        },
        
        reloadPhrases: function() {
            SKApp.server.api(
                'mail/getPhrases',
                {
                    id: this.viewObject.getCurentEmailSubjectId()
                }, 
                function (response) {
                    if (undefined !== response.data) {
                        for (var i in response.data) {
                            
                            var phrase = new SKMailPhrase();                            
                            phrase.mySqlId = parseInt(i);
                            phrase.text = response.data[i];
                            
                            SKApp.user.simulation.mailClient.availablePhrases.push(phrase);
                        }
                        for (var i in response.addData) {
                            var string = response.addData[i];
                            
                            var phrase = new SKMailPhrase();
                            phrase.mySqlId = parseInt(i);
                            phrase.text = response.addData[i];
                            
                            SKApp.user.simulation.mailClient.availableAdditionalPhrases.push(phrase);
                        }
                    }
                },
                false
            ); 
                
            this.viewObject.reloadPhrases();
        },
        
        /**
         * Search throw list of avalibabte to add to email text phrases
         */
        getAvailablePhraseByMySqlId: function(phraseId) {
            var phrases = this.availablePhrases;
            for (var i in phrases) {
                // keep '==' not strict!
                if (phrases[i].mySqlId == phraseId) {
                    return phrases[i];
                }
            }
            
            var addPhrases = this.availableAdditionalPhrases;
            for (var j in addPhrases) {
                // keep '==' not strict!
                if (addPhrases[j].mySqlId == phraseId) {
                    return addPhrases[j];
                }
            }
            
            return undefined;
        },
        
        /**
         * Search throw already used in email text phrases
         */
        getUsedPhraseByUid: function(phraseUid) {

            var phrases = this.newEmailUsedPhrases;
            for (var i in phrases) {
                // keep '==' not strict!
                if (phrases[i].uid == phraseUid) {
                    return phrases[i];
                }
            }
            
            return undefined;
        },
        
        addPhraseToEmail: function(phrase) {
            this.newEmailUsedPhrases.push(phrase);
            this.viewObject.renderAddPhraseToEmail(phrase);
        },
        
        /**
         * @var SKMailPhrase phrase
         */
        removePhraseFromEmail: function(phrase) {
            this.viewObject.removePhraseFromEmail(phrase);
            
            var phrases = this.newEmailUsedPhrases;
            for (var i in phrases) {
                // keep '==' not strict!
                if (phrases[i].uid === phrase.uid) {
                    phrases.splice(i, 1);
                    return true;
                }
            }
        },
        
        uploadAttachmentsList: function() {
            SKApp.server.api(
                'myDocuments/getList',
                {}, 
                function (response) {
                    if (undefined !== response.data) {
                        for (var i in response.data) {
                            
                            var attach = new SKAttachment();                            
                            attach.fileMySqlId = response.data[i].id;
                            attach.label       = response.data[i].name;
                            
                            SKApp.user.simulation.mailClient.availableAttachments.push(attach);
                        }
                    }
                },
                false
            ); 
                
            
        },
        
        getCharacterById: function(id) {
            for (var i in this.defaultRecipients) {
                // keep not strong comparsion
                if (this.defaultRecipients[i].mySqlId == id) {
                    return this.defaultRecipients[i];
                }
            }  
            
            return undefined;
        },
        
        /**
         * @var integer fileId
         */
        getAttahmentByFileID: function(fileId) {
            for (var i in this.availableAttachments) {
                // keet not strict comparsion ('==') !
                if (this.availableAttachments[i] == fileID) {
                    return this.availableAttachments[i];
                }
            }
            
            return undefined;
        },
        
        sendNewCustomEmail: function(emailToSave) {
            //this.lastNotSavedEmail = emailToSave;

            SKApp.server.api(
                'mail/sendMessage',
                {
                    copies:     emailToSave.getCopyToIdsString(),
                    fileId:     emailToSave.getAttachmentId(),
                    messageId:  '',
                    phrases:    emailToSave.getPhrasesIdsString(),
                    receivers:  emailToSave.getRecipientIdsString(),
                    subject:    emailToSave.subject.characterSubjectId,
                    timeString:	SKApp.user.simulation.getGameMinutes()
                },
                function (responce) {
                    // keep non strict comparsion
                    if (1 == responce.result) {
                        SKApp.user.simulation.mailClient.getSendedFolderEmails(); 
                    } else {
                        SKApp.user.simulation.mailClient.message_window =
                            SKApp.user.simulation.mailClient.message_window || new SKDialogView({
                            'message': 'Не удалось отправить письмо.',
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
                    
                    //SKApp.user.simulation.mailClient.lastNotSavedEmail = undefined;
                    
                },
                false
            );
        },

        'renderWriteReplyEmailScreen': function() {
            
        },
        
        'renderWriteReplyAllEmailScreen': function() {
            
        },
        
        'renderForwardEmailScreen': function() {
            
        },
        
        'renderAllToPlanPopUp': function() {
            
        },
        
        'closeMailClientWindow': function() {
            
        },
        
        // ------------------------------------------------------
        
        renderMailClientFunctionalButtons: function(buttonsToDisplay) {
            if ('undefined' === typeof buttonsToDisplay) {
                buttonsToDisplay = [];
            }
        },
        
        toggleWindow: function() {
            if ('undefined' === typeof this.window) {
                this.openWindow();
            } else {
                if (1 === this.window.active) {
                    this.window = 'undefined';
                    this.closeWindow();
                } else {
                    this.activateWidow();
                }
            }
        },

        openWindow: function() {
            this.getDataForInitialScreen();
        },
        
        'activateWindow': function() {
            
        },
        
        /**
         * Close mailClient screen as our virtual application
         * Maybe in future we will habe some logic here
         */
        closeWindow: function() {
            this.viewObject.hideMailClientScreen();
        }
    });
})();
