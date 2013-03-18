/*global Backbone, SKMailClientView, SKMailFolder, SKMailSubject, SKEmail, SKApp, SKDialogView, SKMailAddToPlanDialog*/

var SKMailClient;

define(["game/models/SKMailFolder", "game/models/SKMailSubject","game/models/SKCharacter", "game/models/SKMailPhrase" ], function () {
    "use strict";
    /**
     * @class SKMailClient
     * @augments Backbone.Model
     */
    SKMailClient = Backbone.Model.extend(
        /** @lends SKMailClient.prototype */
        {

            // --------------------------------------------------

            // @var string

            aliasFolderInbox:'INBOX',

            // @var string
            aliasFolderDrafts:'DRAFTS',

            // @var string
            aliasFolderSended:'SENDED',

            // @var string
            aliasFolderTrash:'TRASH',

            // @var string
            codeFolderInbox:1,

            // @var string
            codeFolderDrafts:2,

            // @var string
            codeFolderSended:3,

            // @var string
            codeFolderTrash:4,

            // --------------------------------------------------

            emailTypeNew:'new',

            // --------------------------------------------------

            // @var string
            aliasButtonNewEmail:'NEW_EMAIL',

            // @var string
            aliasButtonReply:'REPLY_EMAIL',

            // @var string
            aliasButtonReplyAll:'REPLY_ALL_EMAIL',

            // @var string
            aliasButtonForward:'FORWARD_EMAIL',

            // @var string
            aliasButtonSend:'SEND_EMAIL',

            // @var string
            aliasButtonSendDraft:'SEND_DRAFT_EMAIL',

            // @var string
            aliasButtonSaveDraft:'SAVE_TO_DRAFTS',

            // @var string
            aliasButtonRestore:'RESTORE',

            // @var string
            aliasButtonAddToPlan:'ADD_TO_PLAN',

            // @var string
            aliasButtonMoveToTrash:'MOVE_TO_TRASH',

            // unfortunatey this checnge context inside new Array, so I need to use literals
            iconsForInboxScreenArray:[
                'NEW_EMAIL',
                'REPLY_EMAIL',
                'REPLY_ALL_EMAIL',
                'FORWARD_EMAIL',
                'ADD_TO_PLAN',
                'MOVE_TO_TRASH'
            ],
            iconsForTrashScreenArray:[
                'NEW_EMAIL',
                'REPLY_EMAIL',
                'REPLY_ALL_EMAIL',
                'FORWARD_EMAIL',
                'ADD_TO_PLAN',
                'RESTORE'
            ],

            iconsForWriteEmailScreenArray:[
                'SEND_EMAIL',
                'SAVE_TO_DRAFTS'
            ],

            iconsForDraftsScreenArray:[
                'NEW_EMAIL',
                'SEND_DRAFT_EMAIL'
            ],

            iconsForSendedScreenArray:[
                'NEW_EMAIL'
            ],

            // --------------------------------------------------

            // @var string
            screenInboxList:'SCREEN_INBOX_LIST',

            // @var string
            screenDraftsList:'SCREEN_DRAFTS_LIST',

            // @var string
            screenSendedList:'SCREEN_SENDED_LIST',

            // @var string
            screenTrashList:'SCREEN_TRASH_LIST',

            // @var string
            screenAddToPlan:'SCREEN_ADD_TO_PLAN',

            // @var string
            screenReadEmail:'SCREEN_READ_EMAIL',

            // @var string
            screenWriteNewCustomEmail:'SCREEN_WRITE_NEW_EMAIL',

            // @var string
            screenWriteReply:'SCREEN_WRITE_REPLY',

            // @var string
            screenWriteReplyAll:'SCREEN_WRITE_REPLY_ALL',

            // @var string
            screenWriteForward:'SCREEN_WRITE_FORWARD',

            // --------------------------------------------------

            // @var stringone of 'screenXXX' literals
            currentScreen:undefined,

            // @var SkWindow
            windowObject:undefined,

            // @var SKEmail
            activeEmail:undefined,

            // @var string
            activeScreen:undefined,

            /** @var Array.<SKMailFolder> */
            folders:[],

            // @var array of SkCharacter
            defaultRecipients:[],

            // use just to store avaliable for current new email subjects according recipients list
            // refreshed for each new email and each recipients list change
            // @var array of
            availableSubjects:[],

            // @var array of SKMailAttachment
            availableAttachments:[],

            // @var array of SKMailPhrase
            availablePhrases:[],

            // @var array of SKMailPhrase
            // this is ',', '.', ':' etc. - symbols for any phrases set
            availableAdditionalPhrases:[],

            // @var array of SKMailPhrase
            newEmailUsedPhrases:[],

            // @var array of SKMailAttachment
            newEmailAttachment:undefined,

            // @var array of SKMailSubject
            newEmailSubjectId:undefined,

            // @var undefined | SKEmail
            lastNotSavedEmail:undefined,

            // @var array of SKMailTAsk
            availaleActiveEmailTasks:[],

            // @var string
            messageForNewEmail:'',

            // @var integer, to keep window_uid when user switched to mailPlan or mailNew
            window_uid: undefined,

            // -------------------------------------------------

            /**
             * Constructor
             * @method initialize
             */
            initialize:function () {
                this.folders[this.aliasFolderInbox] = new SKMailFolder();
                this.folders[this.aliasFolderInbox].alias = this.aliasFolderInbox;

                this.folders[this.aliasFolderDrafts] = new SKMailFolder();
                this.folders[this.aliasFolderDrafts].alias = this.aliasFolderDrafts;

                this.folders[this.aliasFolderSended] = new SKMailFolder();
                this.folders[this.aliasFolderSended].alias = this.aliasFolderSended;

                this.folders[this.aliasFolderTrash] = new SKMailFolder();
                this.folders[this.aliasFolderTrash].alias = this.aliasFolderTrash;

                // init folder names
                this.getInboxFolder().name  = 'Входящие';
                this.getDraftsFolder().name = 'Черновики';
                this.getSendedFolder().name = 'Отправленные';
                this.getTrashFolder().name  = 'Корзина';
            },

            /**
             * @method
             * @return string,
             */
            getActiveSubscreenName:function () {
                if (undefined === this.activeScreen) {
                    return 'mailMain';
                }
                if ('SCREEN_ADD_TO_PLAN' === this.activeScreen) {
                    return 'mailPlan';
                }
                if ('SCREEN_DRAFTS_LIST' === this.activeScreen) {
                    return 'mailMain';
                }
                if ('SCREEN_INBOX_LIST' === this.activeScreen) {
                    return 'mailMain';
                }
                if ('SCREEN_READ_EMAIL' === this.activeScreen) {
                    return 'mailPreview';
                }
                if ('SCREEN_SENDED_LIST' === this.activeScreen) {
                    return 'mailMain';
                }
                if ('SCREEN_TRASH_LIST' === this.activeScreen) {
                    return 'mailMain';
                }
                if ('SCREEN_WRITE_FORWARD' === this.activeScreen) {
                    return 'mailNew';
                }
                if ('SCREEN_WRITE_NEW_EMAIL' === this.activeScreen) {
                    return 'mailNew';
                }
                if ('SCREEN_WRITE_REPLY' === this.activeScreen) {
                    return 'mailNew';
                }
                if ('SCREEN_WRITE_REPLY_ALL' === this.activeScreen) {
                    return 'mailNew';
                }

                return 'mailMain';
            },

            /**
             * @method
             * @param id
             * @returns {*}
             */
            getMailTaskByMySqlId:function (id) {
                for (var i in this.availaleActiveEmailTasks) {
                    if (parseInt(this.availaleActiveEmailTasks[i].mySqlId, 10) === parseInt(id,10)) {
                        return this.availaleActiveEmailTasks[i];
                    }
                }

                return undefined;
            },

            /**
             * @method
             * @return SkMailFolder
             */
            getInboxFolder:function () {
                return this.folders[this.aliasFolderInbox];
            },

            /**
             * @method
             * @return SkMailFolder
             */
            getDraftsFolder:function () {
                return this.folders[this.aliasFolderDrafts];
            },

            /**
             * @method
             * @return SkMailFolder
             */

            getSendedFolder:function () {
                return this.folders[this.aliasFolderSended];
            },

            /**
             * @method
             * @return SkMailFolder
             */
            getTrashFolder:function () {
                return this.folders[this.aliasFolderTrash];
            },

            /**
             * CleanUp 'folderAlias' folder
             * Push all emails from 'emails' to 'folderAlias' folder
             *
             * @method
             * @param folderAlias
             * @param emailsData
             */
            setEmailsToFolder:function (folderAlias, emailsData) {
                var me = this;
                this.folders[folderAlias].emails = [];

                for (var id in emailsData) {
                    if (emailsData.hasOwnProperty(id)) {

                        var subject = new SKMailSubject();
                        var emailData = emailsData[id];
                        subject.text = emailData.subject;

                        var email               = new SKEmail();
                        email.mySqlId           = emailData.id;
                        email.text              = emailData.text;
                        email.is_readed         = (1 === parseInt(emailData.readed, 10));
                        email.is_has_attachment = (1 === parseInt(emailData.attachments, 10));
                        email.sendedAt          = emailData.sentAt;
                        email.subject           = subject;
                        email.setSenderEmailAndNameStrings(emailData.sender);

                        var attachment = new SKAttachment();
                        attachment.label       = emailData.attachmentName;
                        attachment.fileMySqlId = emailData.attachmentFileId;

                        email.attachment = attachment;

                        var recipiens = emailData.receiver.split(',');
                        for (var i in recipiens) {                            
                            email.addRecipientEmailAndNameStrings(recipiens[i]);
                        }

                        if (emailData.copy !== undefined) {
                            var copies = emailData.copy.split(',');
                            for (var i in copies) {
                                email.addCopyEmailAndNameStrings(copies[i]);
                            }
                        }

                        if (undefined !== emailData.reply) {
                            email.previouseEmailText = emailData.reply;
                        }

                        this.folders[folderAlias].emails.push(email);
                    }
                }
            },

            /**
             * @method
             * @param string activeScreenAlias, this.screenXxx literals
             */
            setActiveScreen:function (activeScreenAlias) {
                this.activeScreen = activeScreenAlias;
            },

            /**
             * @method
             * @return: $.xhr array, Skiliks API responce
             */
            getDataForReplyToActiveEmail:function () {
                var mailClient = this;

                return SKApp.server.api(
                    'mail/reply',
                    {
                        id:mailClient.activeEmail.mySqlId
                    },
                    function (response) {
                        if (1 == response.result) {
                            return response;
                        } else {
                            throw "Can`t initialize responce email. Model. #1";
                        }
                    },
                    false
                );
            },

            /**
             * @method
             * @return: $.xhr array, Skiliks API responce
             */
            getDataForReplyAllToActiveEmail:function () {
                var mailClient = this;

                return SKApp.server.api(
                    'mail/replyAll',
                    {
                        id:mailClient.activeEmail.mySqlId
                    },
                    function (response) {
                        if (1 === response.result) {
                            return response;
                        } else {
                            throw "Can`t initialize responce email. Model. #2";
                        }
                    },
                    false
                );
            },

            /**
             * @method
             * @returns {$.xhr}
             */
            getDataForForwardActiveEmail:function () {
                var mailClient = this;

                return SKApp.server.api(
                    'mail/forward',
                    {
                        id:mailClient.activeEmail.mySqlId
                    },
                    function (response) {
                        if (1 === response.result) {
                            return response;
                        } else {
                            throw "Can`t initialize responce email. Model. #3";
                        }
                    },
                    false
                );
            },

            /**
             * @method
             */
            getDataForInitialScreen:function () {
                var me = this;

                // mark INCOME folders as active
                this.folders[this.aliasFolderInbox].isActive = true;
                var folder_to_load = 4;
                var onSent = function () {
                    folder_to_load--;
                    if (folder_to_load === 0) {
                        me.trigger('init_completed');
                    }
                    return folder_to_load;
                };

                this.getInboxFolderEmails(onSent);
                this.getDraftsFolderEmails(onSent);
                this.getSendedFolderEmails(onSent);
                this.getTrashFolderEmails(onSent);
            },

            // todo: combine all getXxxFolderEmails() to one method.

            /**
             * @method
             * @param cb
             */
            getInboxFolderEmails:function (cb) {
                var me = this;
                SKApp.server.api(
                    'mail/getMessages',
                    {
                        folderId:me.codeFolderInbox,
                        order:1,
                        order_type:0
                    },
                    function (responce) {
                        me.updateInboxFolderEmails(responce.messages);
                        if (undefined != cb) {
                            cb();
                        }
                    }
                );
            },

            /**
             * @method
             * @param cb
             */
            getDraftsFolderEmails:function (cb) {
                SKApp.server.api(
                    'mail/getMessages',
                    {
                        folderId:SKApp.simulation.mailClient.codeFolderDrafts,
                        order:2,
                        order_type:0
                    },
                    function (responce) {
                        SKApp.simulation.mailClient.updateDraftsFolderEmails(responce.messages);
                        if (undefined != cb) {
                            cb();
                        }
                    }
                );
            },

            /**
             * @method
             * @param cb
             */
            getSendedFolderEmails:function (cb) {
                SKApp.server.api(
                    'mail/getMessages',
                    {
                        folderId:SKApp.simulation.mailClient.codeFolderSended,
                        order:-1,
                        order_type:0
                    },
                    function (responce) {
                        SKApp.simulation.mailClient.updateSendedFolderEmails(responce.messages);
                        if (undefined != cb) {
                            cb();
                        }
                    }
                );
            },

            /**
             * @method
             * @param cb
             */
            getTrashFolderEmails:function (cb) {
                SKApp.server.api(
                    'mail/getMessages',
                    {
                        folderId:SKApp.simulation.mailClient.codeFolderTrash,
                        order:-1,
                        order_type:0
                    },
                    function (responce) {
                        SKApp.simulation.mailClient.updateTrashFolderEmails(responce.messages);
                        if (undefined != cb) {
                            cb();
                        }
                    }
                );
            },

            // todo: combine all updateXxxFolderEmails() to one method.

            /**
             * @method
             * @param messages
             */
            updateInboxFolderEmails:function (messages) {
                this.setEmailsToFolder(this.aliasFolderInbox, messages);
            },

            /**
             * @method
             * @param messages
             */
            updateDraftsFolderEmails:function (messages) {
                this.setEmailsToFolder(this.aliasFolderDrafts, messages);
            },

            /**
             * @method
             * @param messages
             */
            updateSendedFolderEmails:function (messages) {
                this.setEmailsToFolder(this.aliasFolderSended, messages);
            },

            /**
             * @method
             * @param messages
             */
            updateTrashFolderEmails:function (messages) {
                this.setEmailsToFolder(this.aliasFolderTrash, messages);
            },

            /**
             * @method
             * @param emails
             * @returns {boolean}
             */
            setActiveEmailFromArray:function (emails) {
                if (0 === emails.length) {
                    this.activeEmail = undefined;
                    return false;
                }

                for (var key in emails) {
                    this.activeEmail = emails[key];
                    return true;
                }
            },

            /**
             * @method
             * @returns {*}
             */
            getActiveEmailId:function () {
                if (undefined === this.activeEmail) {
                    return undefined;
                }

                return this.activeEmail.mySqlId;
            },

            /**
             * @method
             * @param email
             */
            setActiveEmail:function (email) {
                // active email or readed or new writed in any case
                if (undefined !== email) {
                    if(!email.is_readed){
                        SKApp.server.api(
                            'mail/MarkRead',
                            {
                                id:email.mySqlId
                            },
                            function () {

                            }
                        );
                    }
                    email.is_readed = true;
                }
                this.trigger('mail:update_inbox_counter');

                this.activeEmail = email;

            },

            /**
             * @method
             * @param alias
             */
            setActiveFolder:function (alias) {
                for (var i in this.folders) {
                    this.folders[i].isActive = false;
                    if (alias === i) {
                        this.folders[i].isActive = true;
                    }
                }
            },

            /**
             * Returns active folder
             *
             * @method
             * @return {SKMailFolder|undefined}
             */
            getActiveFolder:function () {
                for (var i in this.folders) {
                    if (this.folders[i].isActive) {
                        return this.folders[i];
                    }
                }
                return undefined;
            },

            /**
             * Returns email bi it`s id from anu folder.
             * By the way, any email stored in single folder in any moment of time
             *
             * @method
             */
            getEmailByMySqlId:function (emailId) {
                for (var alias in this.folders) {
                    if (this.folders.hasOwnProperty(alias)) {
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

            /**
             * @method
             * @returns {*}
             */
            getSimulationMailClientWindow:function () {
                var windows = SKApp.simulation.window_set.where({name:'mailEmulator'});

                if (undefined === windows || 0 === windows.length) {
                    console.warn('There is no active window object for mailClient.');
                }

                return windows[0];
            },

            /**
             * @method
             * @param newSubscreen
             * @param {integer} emailId
             */
            setWindowsLog:function (newSubscreen, emailId) {
                var window = this.getSimulationMailClientWindow();

                SKApp.simulation.windowLog.deactivate(window);

                if ((window.get('subname') == 'mailMain' && 'mailNew' == newSubscreen) ||
                    (window.get('subname') == 'mailMain' && 'mailPlan' == newSubscreen)) {
                    this.window_uid = parseInt(window.window_uid);
                    window.updateUid();
                } else if ((window.get('subname') == 'mailNew' && 'mailMain' == newSubscreen) ||
                    (window.get('subname') == 'mailPlan' && 'mailMain' == newSubscreen)) {
                    window.window_uid = parseInt(this.window_uid);
                }

                window.set('id', newSubscreen);
                window.set('subname', newSubscreen);
                window.set('params', {'mailId':emailId});

                SKApp.simulation.windowLog.activate(window);
            },

            /**
             * @method
             * @param attachmentId
             */
            saveAttachmentToMyDocuments:function (attachmentId) {
                // call saveAttachment URL
                SKApp.server.api(
                    'myDocuments/add',
                    {
                        attachmentId:attachmentId
                    },
                    function (response) {
                        // and display message for user
                        if (response.result === 1) {
                            SKApp.simulation.mailClient.message_window = new SKDialogView({
                                'message':'Файл был успешно сохранён в папку Мои документы.',
                                'buttons':[
                                    {
                                        'value':'Окей',
                                        'onclick':function () {
                                            delete SKApp.simulation.mailClient.message_window;
                                        }
                                    }
                                ]
                            });
                            SKApp.simulation.documents.fetch();
                        } else {
                            throw 'Can not add document';
                        }
                    });
            },

            /**
             * @method
             * @param id
             * @returns {*}
             */
            getRecipientByMySqlId:function (id) {
                for (var i in this.defaultRecipients) {
                    // keep non strict!
                    if (id == this.defaultRecipients[i].mySqlId) {
                        return this.defaultRecipients[i];
                    }
                }
                return undefined;
            },

            /**
             * @method
             * @param name
             * @returns {*}
             */
            getRecipientByName:function (name) {
                for (var i in this.defaultRecipients) {
                    // keep non strict!
                    if (name == this.defaultRecipients[i].name) {
                        return this.defaultRecipients[i];
                    }
                }
                return undefined;
            },

            /**
             * @method
             * @param name
             * @returns {*}
             */
            findRecipientByName : function (name) {

                var defaultRecipients = this.defaultRecipients;
                for (var j in defaultRecipients) {
                    // get IDs of character by label text comparsion
                    if (name === defaultRecipients[j].getFormatedForMailToName()) {
                        return defaultRecipients[j].mySqlId;
                    }
                }
            },

            /**
             * @method
             */
            updateRecipientsList:function (cb) {
                var me = this;
                SKApp.server.api(
                    'mail/getReceivers',
                    {},
                    function (response) {
                        if (undefined !== response.data) {
                            me.defaultRecipients = [];
                            for (var i in response.data) {
                                var string = response.data[i];

                                var character = new SKCharacter();
                                character.mySqlId = i;
                                character.excelId = i;
                                character.name = $.trim(string.substr(0, string.indexOf('<')));
                                character.email = $.trim(string.substr(string.indexOf('<'), string.length));
                                character.email = character.email.replace('<', '');
                                character.email = character.email.replace('>', '');

                                me.defaultRecipients.push(character);
                            }
                        }
                        if (cb !== undefined) {
                            cb();
                        }
                    },
                    false
                );
            },

            /**
             * @method
             * @returns {Array}
             */
            getFormatedCharacterList:function () {
                var list = [];
                for (var i in this.defaultRecipients) {
                    // non strict "!=" is important!
                    if ('' != this.defaultRecipients[i].name && '' != this.defaultRecipients[i].email) {
                        list.push(this.defaultRecipients[i].getFormatedForMailToName());
                    }
                }

                return list;
            },

            /**
             * @method
             * @param {Array.<integer>} recipientIds
             * @param action
             * @param {undefined|SKMailSubject} parent_subject
             */
            reloadSubjectsWithWarning:function (recipientIds, action, parent_subject, callback, el_tag, updateSubject) {
                var mailClient = this;

                var checkValue = 1;
                if ('add' === action || 'add_fwd' === action) {
                    checkValue = 0;
                }
                // display warning only if user add extra recipients
                if (recipientIds[0] === mailClient.findRecipientByName($(el_tag).text()) &&  this.isNotEmptySubject()) {
                    if(action !== 'add_fwd' && action !== 'delete_fwd') {
                    this.message_window = new SKDialogView({
                        'message':'Если вы измените список адресатов, то поменяются доступные Вам темы письма, очистится список доступных фраз и тескт письма.',
                        'buttons':[
                            {
                                'value':'Продолжить',
                                'onclick':function () {
                                    delete mailClient.message_window;
                                    if(recipientIds.length !== 0){mailClient.reloadSubjects(recipientIds, parent_subject);}
                                    $("#mailEmulatorNewLetterText").html('');
                                    if ('add' === action || 'add_fwd' === action) {
                                        callback();
                                    }else if('delete'){
                                        $("#MailClient_RecipientsList")[0].removeTag(el_tag);
                                        if(typeof updateSubject === 'function'){
                                            updateSubject();
                                        }
                                    }
                                }
                            },
                            {
                                'value':'Вернуться',
                                'onclick':function () {
                                    //mailClient.trigger('mail:return_last_subject');
                                    delete mailClient.message_window;

                                }
                            }
                        ]
                    });}else{
                        if(action === 'delete_fwd' || action === 'add_fwd'){return true;}
                    }
                    return false;
                } else {
                    /*mailClient.reloadSubjects(recipientIds, parent_subject);
                    if(action !== 'add_fwd' && action !== 'delete_fwd') {
                        $("#mailEmulatorNewLetterText").html('');
                    }*/
                    return true;
                }
            },

            /**
             * @method
             * @param recipientIds
             * @param subject
             */
            reloadSubjects:function (recipientIds, subject) {
                if(recipientIds.length <= 0) {
                    //$("#MailClient_NewLetterSubject option[value!='0']").remove();//Todo:Заменить
                    SKApp.simulation.mailClient.availableSubjects = [];
                    return;
                }
                this.messageForNewEmail = '';
                var me = this;
                SKApp.server.api(
                    'mail/getThemes',
                    {
                        receivers:recipientIds.join(','), // implode()
                        parentSubjectId: subject !== undefined ? subject.parentMySqlId : undefined
                    },
                    function (response) {
                        if (undefined !== response.data) {
                            // clean up list
                            SKApp.simulation.mailClient.availableSubjects = [];

                            for (var i in response.data) {
                                var string = response.data[i];

                                var subject = new SKMailSubject();
                                subject.characterSubjectId = i;
                                subject.text = response.data[i];

                                SKApp.simulation.mailClient.availableSubjects.push(subject);
                            }
                            me.trigger('mail:subject_list_in_model_updated');
                        }
                    },
                    false
                );
            },

            /**
             * @method
             * @param array
             */
            setRegularAvailablePhrases:function (array) {
                this.messageForNewEmail = '';
                this.availablePhrases = []; // clean-up old phrases

                for (var i in array) {

                    var phrase = new SKMailPhrase();
                    phrase.mySqlId = parseInt(i);
                    phrase.text = array[i];

                    this.availablePhrases.push(phrase);
                }
            },

            /**
             * @method
             * @param array
             */
            setAdditionalAvailablePhrases:function (array) {
                this.availableAdditionalPhrases = []; // clean-up old phrases

                for (var i in array) {
                    var phrase = new SKMailPhrase();
                    phrase.mySqlId = parseInt(i);
                    phrase.text = array[i];

                    this.availableAdditionalPhrases.push(phrase);
                }
            },

            /**
             * Receives and updates phrase list and message for email
             *
             * @method
             * @param subjectId
             * @param callback
             */
            getAvailablePhrases:function (subjectId, callback) {
                var mailClient = this;
                SKApp.server.api(
                    'mail/getPhrases',
                    {
                        id:subjectId
                    },
                    function (response) {
                        if (undefined !== response.data) {
                            mailClient.setRegularAvailablePhrases(response.data);

                            mailClient.setAdditionalAvailablePhrases(response.addData);

                            mailClient.messageForNewEmail = response.message;

                            if(typeof callback === 'function'){
                                callback();
                            }

                        }
                        mailClient.trigger('mail:available_phrases_reloaded');
                    }
                );

                // throw event there - because no matter success or fail request, phrases are need tobe reloaded

            },

            /**
             * Search throw list of avalibabte to add to email text phrases
             *
             * @method
             */
            getAvailablePhraseByMySqlId:function (phraseId) {
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
             *
             * @method
             */
            getUsedPhraseByUid:function (phraseUid) {

                var phrases = this.newEmailUsedPhrases;
                for (var i in phrases) {
                    // keep '==' not strict!
                    if (phrases[i].uid == phraseUid) {
                        return phrases[i];
                    }
                }

                return undefined;
            },

            /**
             * @method
             * @param cb
             */
            uploadAttachmentsList:function (cb) {
                SKApp.server.api(
                    'myDocuments/getList',
                    {},
                    function (response) {
                        if (undefined !== response.data) {
                            for (var i in response.data) {

                                var attach = new SKAttachment();
                                attach.fileMySqlId = response.data[i].id;
                                attach.label = response.data[i].name;

                                SKApp.simulation.mailClient.availableAttachments.push(attach);
                            }
                            cb();
                        }
                    }
                );

            },

            /**
             * @method
             * @param id
             * @returns {*}
             */
            getCharacterById:function (id) {
                for (var i in this.defaultRecipients) {
                    // keep not strong comparsion
                    if (this.defaultRecipients[i].mySqlId == id) {
                        return this.defaultRecipients[i];
                    }
                }

                return undefined;
            },

            /**
             * What is it?
             *
             * @method
             * @param emailToSave
             * @return {Object}
             */
            combineMailDataByEmailObject:function (emailToSave) {
                var mailId = '';
                if (this.activeScreen === this.screenWriteForward ||
                    this.activeScreen === this.screenWriteReply ||
                    this.activeScreen === this.screenWriteReplyAll) {
                    if (undefined !== this.activeEmail) {
                        mailId = this.activeEmail.mySqlId;
                    }
                }

                var type = '';
                if (this.activeScreen == this.screenWriteReplyAll) {
                    type = 'replyAll';
                }
                if (this.activeScreen == this.screenWriteNewCustomEmail) {
                    type = 'new';
                }
                if (this.activeScreen == this.screenWriteReply) {
                    type = 'reply';
                }
                if (this.activeScreen == this.screenWriteForward) {
                    type = 'forward';
                }

                return {
                    copies:emailToSave.getCopyToIdsString(),
                    fileId:emailToSave.getAttachmentId(),
                    messageId:mailId,
                    phrases:emailToSave.getPhrasesIdsString(),
                    receivers:emailToSave.getRecipientIdsString(),
                    subject:emailToSave.subject.characterSubjectId,
                    time:SKApp.simulation.getGameTime(),
                    letterType: type
                };
            },

            /**
             * @method
             * @param emailToSave
             * @param callback
             * @returns {boolean}
             */
            sendNewCustomEmail:function (emailToSave, callback) {
                var me = this;
                if (false === this.validationDialogResult(emailToSave)) {
                    return false;
                }

                SKApp.server.api(
                    'mail/sendMessage',
                    this.combineMailDataByEmailObject(emailToSave),
                    function (response) {
                        // keep non strict compassion
                        if (1 === response.result) {
                            var window = me.getSimulationMailClientWindow();
                            window.set('params', {'mailId': response.messageId});
                            me.getSendedFolderEmails(callback); // callback is usually 'render active folder'
                        } else {
                            me.message_window =
                                me.message_window || new SKDialogView({
                                    'message':'Не удалось отправить письмо.',
                                    'buttons':[
                                        {
                                            'value':'Окей',
                                            'onclick':function () {
                                                delete SKApp.simulation.mailClient.message_window;
                                            }
                                        }
                                    ]
                                });
                        }
                    }
                );
            },

            /**
             * @method
             * @param emailToSave
             * @returns {boolean}
             */
            validationDialogResult:function (emailToSave) {
                var mailClient = this;

                // validation {
                // email.recipients
                if (0 == emailToSave.recipients.length) {
                    mailClient.message_window = new SKDialogView({
                        'message':'Добавьте адресата письма.',
                        'buttons':[
                            {
                                'value':'Окей',
                                'onclick':function () {
                                    delete mailClient.message_window;
                                }
                            }
                        ]
                    });
                    return false;
                }

                // email.sunbject
                if (false === emailToSave.isSubjectValid()) {
                    mailClient.message_window = new SKDialogView({
                        'message':'Укажите тему письма.',
                        'buttons':[
                            {
                                'value':'Окей',
                                'onclick':function () {
                                    delete mailClient.message_window;
                                }
                            }
                        ]
                    });
                    return false;
                }
                // validation }

                return true;
            },

            /**
             * @method
             * @param emailToSave
             * @param cb
             * @returns {boolean}
             */
            saveToDraftsEmail:function (emailToSave, cb) {

                var mailClient = this;

                if (false === this.validationDialogResult(emailToSave)) {
                    return false;
                }

                SKApp.server.api(
                    'mail/saveDraft',
                    mailClient.combineMailDataByEmailObject(emailToSave),
                    function (responce) {
                        // keep non strict comparsion
                        if (1 == responce.result) {
                            var window = mailClient.getSimulationMailClientWindow();
                            window.set('params', {'mailId': responce.messageId});
                            mailClient.getDraftsFolderEmails();
                            cb();
                        } else {
                            mailClient.message_window = new SKDialogView({
                                'message':'Не удалось сохранить письмо.',
                                'buttons':[
                                    {
                                        'value':'Окей',
                                        'onclick':function () {
                                            delete mailClient.message_window;
                                        }
                                    }
                                ]
                            });
                        }
                    }
                );
                return true;
            },

            /**
             * @method
             */
            openWindow: function () {
                this.getDataForInitialScreen();
                //this.trigger('init_completed');
            },

            /**
             * To rewrite
             * @method
             * @return {Boolean}
             */
            isNotEmptySubject:function(){
                return $("#MailClient_NewLetterSubject").data('ddslick').selectedData !== null &&
                    $("#MailClient_NewLetterSubject").data('ddslick').selectedData.value !== 0;
            }
        });
    return SKMailClient;
});

