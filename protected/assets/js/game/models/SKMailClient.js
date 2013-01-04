/* 
 * 
 */
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
        aliasButtonNewEmail:   'NEW_EMAIL',

        // @var string
        aliasButtonReply:      'REPLY_EMAIL',

        // @var string
        aliasButtonReplyAll:   'REPLY_ALL_EMAIL',

        // @var string
        aliasButtonForward:   'FORWARD_EMAIL',

        // @var string
        aliasButtonSend:      'SEND_EMAIL',

        // @var string
        aliasButtonSendDraft: 'SEND_DRAFT_EMAIL',

        // @var string
        aliasButtonSaveDraft: 'SAVE_TO_DRAFTS',
        
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
        folders: new Array(),
        
        // @var array of SkCharacter
        defaultRecipients: new Array(),
        
        // @var array of 
        availableSubjects: new Array(),
        
        // @var array of 
        availableAttachments: new Array(),
        
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
            folderId = parseInt(folderId);
            if (1 == folderId) {
                return this.aliasFolderIncome;
            }  
            if (2 == folderId) {
                return this.aliasFolderSended;
            }  
            if (3 == folderId) {
                return this.aliasFolderDrafts;
            }  
            if (4 == folderId) {
                return this.aliasFolderTrash;
            }  
        },
        
        /**
         * CleanUp 'folderAlias' folder
         * Push all emails from 'emails' to 'folderAlias' folder
         * 
         * @param string folderAlias
         * @param mixed array emails
         */
        setEmailsToFolder: function(folderAlias, emailsData) { 

            this.folders[folderAlias].emails = new Array();
            
            for (var id in emailsData) {
                var subject = new SKMailSubject();
                subject.text = emailsData[id].subject;

                var email = new SKEmail();
                email.mySqlId             = emailsData[id].id;
                email.is_readed           = (1 == parseInt(emailsData[id].readed));
                email.is_has_attachment   = (1 == parseInt(emailsData[id].attachments));
                email.sendedAt            = emailsData[id].receivingDate;
                email.subject             = subject;
                email.senderNameString    = emailsData[id].sender;
                email.recipientNameString = emailsData[id].receiver;
                
                this.folders[folderAlias].addEmail(email);
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
                { sid: session.getSid() }, 
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
            if (0 < this.folders[this.aliasFolderIncome].emails.length) {
                for (var key in this.folders[this.aliasFolderIncome].emails) {
                    this.setActiveEmail(this.folders[this.aliasFolderIncome].emails[key]);
                    break;
                }
            }
            // set as active first letter in Income folder }
            
            this.viewObject.renderMailClientScreenBase();
            this.viewObject.updateFolderLabels();
            this.renderFolder(this.aliasFolderIncome);
        },
        
        renderFolder: function(folderAlias) {
            console.log(folderAlias);
            if (this.aliasFolderIncome == folderAlias) {
                console.log('way 1');
                this.viewObject.renderIncomeFolder();
                this.setActiveScreen(this.screenIncomeList);
            }
        },
        
        /**
         * Returns email bi it`s id from anu folder.
         * By the way, any email stored in single folder in any moment of time
         */
        getEmailById: function(emailId) {
            for (var alias in this.folders) {
                var emais = this.folders[alias].emails;
                for (var i in emais) {
                    if (emais[i].mySqlId == emailId) {
                        return emais[i];
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
                    sid:          session.getSid(),
                    attachmentId: attachmentId
                }, 
                function (responce) {
                    // and display message for user
                    SKApp.user.simulation.mailClient.message_window 
                        = SKApp.user.simulation.mailClient.message_window || new SKDialogView({
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
        
        /**
         */
        'renderWriteCustomNewEmailScreen': function() {
            
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
        
        'renderMailClientFunctionalButtons': function(buttonsToDisplay) {
            if ('undefined' == typeof buttonsToDisplay) {
                buttonsToDisplay = new Array();
            }
        },
        
        'toggleWindow': function() {
            if ('undefined' == typeof this.window) {
                this.openWindow();
            } else {
                if (1 == this.window.active) {
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
