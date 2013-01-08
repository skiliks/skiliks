/*global Backbone, SKMailClientView, SKMailFolder, SKMailSubject, SKEmail, SKApp, SKDialogView*/
(function() {
    "use strict";
    window.SKMailClient = Backbone.Model.extend({
        
        // --------------------------------------------------
        
        // @var string

        aliasFolderIncome : 'INCOME',

        // @var string
        aliasFolderDrafts: 'DRAFTS',

        // @var string
        aliasFolderSended: 'SENDED',

        // @var string
        aliasFolderTrash: 'TRASH',
        
        // @var string
        codeFolderIncome : 1,

        // @var string
        codeFolderDrafts: 2,

        // @var string
        codeFolderSended: 3,

        // @var string
        codeFolderTrash: 4,
        
        // --------------------------------------------------

        // @var string
        aliasButtonNewEmail:  'NEW_EMAIL',

        // @var string
        aliasButtonReply:     'REPLY_EMAIL',

        // @var string
        aliasButtonReplyAll:  'REPLY_ALL_EMAIL',

        // @var string
        aliasButtonForward:   'FORWARD_EMAIL',

        // @var string
        aliasButtonSend:      'SEND_EMAIL',

        // @var string
        aliasButtonSendDraft: 'SEND_DRAFT_EMAIL',

        // @var string
        aliasButtonSaveDraft: 'SAVE_TO_DRAFTS',
        
        // @var string
        aliasButtonAddToPlan: 'ADD_TO_PLAN',
        
        // unfortunatey this checnge context inside new Array, so I need to use literals
        iconsForIncomeScreenArray: [
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
            'SEND_DRAFT_EMAIL'
        ],
        
        // --------------------------------------------------
        
        // @var string
        screenIncomeList: 'SCREEN_INCOME_LIST',
        
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
        
        // -------------------------------------------------
        
        initialize: function() {
            this.folders[this.aliasFolderIncome] = new SKMailFolder();
            this.folders[this.aliasFolderDrafts] = new SKMailFolder();
            this.folders[this.aliasFolderSended] = new SKMailFolder();
            this.folders[this.aliasFolderTrash]  = new SKMailFolder();
            

            this.viewObject.setMailClient(this);
        },
        
        /**
         * @return SkMailFolder
         */
        getIncomeFolder: function(){
            return this.folders[this.aliasFolderIncome];
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
            this.getIncomeFolder().name = data[this.codeFolderIncome].name;
            this.getDraftsFolder().name = data[this.codeFolderDrafts].name;
            this.getSendedFolder().name = data[this.codeFolderSended].name;
            this.getTrashFolder().name  = data[this.codeFolderTrash].name;
        },
        
        getFolderAliasById: function(folderId) {
            folderId = parseInt(folderId, 10);
            if (1 === folderId) {
                return this.aliasFolderIncome;
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
                    email.senderNameString    = emailsData[id].sender;
                    email.recipientNameString = emailsData[id].receiver;

                    this.folders[folderAlias].addEmail(email);
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
        
        setActiveEmail: function(email) {
            // active email or readed or new writed in any case
            email.is_readed = true;
            
            this.activeEmail = email;
         },
        
        renderInitialScreen: function(folders, messages) {
            // process and store in model AJAX data {
            this.updateFolders(folders);
            this.setEmailsToFolder(this.aliasFolderIncome, messages);
            // process and store in model AJAX data }
            
            // mark INCOM foldes as active
            this.folders[this.aliasFolderIncome].isActive = true;
            
            // set as active first letter in Income folder {
            var emails = this.folders[this.aliasFolderIncome].emails;
            if (0 < emails.length) {
                for (var key in emails) {
                    if (emails.hasOwnProperty(key)) {
                        this.setActiveEmail(emails[key]);
                        break;
                    }
                }
            }
            // set as active first letter in Income folder }
            
            this.viewObject.renderMailClientScreenBase();
            this.viewObject.updateFolderLabels();
            this.preRenderFolder(this.aliasFolderIncome);
        },
        
        preRenderFolder: function(folderAlias) {
            if (this.aliasFolderIncome === folderAlias) {
                this.viewObject.renderIncomeFolder();                
            }
            
            this.setActiveScreen(folderAlias);
        },
        
        /**
         * Returns email bi it`s id from anu folder.
         * By the way, any email stored in single folder in any moment of time
         */
        getEmailById: function(emailId) {
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
            this.viewObject.renderReadEmail(this.getEmailById(emailId));
            
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
        
        /**
         * @vat SKAttachment attachment
         */
        addAttachment: function(attachment) {
            console.log(attachment.label);
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
