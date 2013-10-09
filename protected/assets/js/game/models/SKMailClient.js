/*global Backbone, SKMailClientView, SKMailFolder, SKMailSubject, SKEmail, SKApp, SKDialogView,
SKMailAddToPlanDialog, define, _, SKAttachment, console, $, */

var SKMailClient;

define(["game/models/SKMailFolder", "game/models/SKMailSubject","game/models/SKCharacter", "game/models/SKMailPhrase" ], function (
    SKMailFolder, SKMailSubject, SKCharacter, SKMailPhrase
) {
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
                'ADD_TO_PLAN',
                'NEW_EMAIL',
                'REPLY_EMAIL',
                'REPLY_ALL_EMAIL',
                'FORWARD_EMAIL',
                'MOVE_TO_TRASH'
            ],
            iconsForTrashScreenArray:[
                'ADD_TO_PLAN',
                'NEW_EMAIL',
                'REPLY_EMAIL',
                'REPLY_ALL_EMAIL',
                'FORWARD_EMAIL',
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

            iconsForEditDraftDraftScreenArray:[
                'SAVE_TO_DRAFTS'
            ],

            iconsForSendedScreenArray:[
                'NEW_EMAIL'
            ],

            // - tutorial scenario:

            iconsForTutorialScenarioFolderInbox: [
                'ADD_TO_PLAN',
                'MOVE_TO_TRASH'
            ],

            iconsForTutorialScenarioFolderTrash:[
                'ADD_TO_PLAN',
                'RESTORE'
            ],

            iconsForTutorialScenarioFolderDrafts:[],

            iconsForTutorialScenarioFolderSend:[],

            // --------------------------------------------------

            // SKEmail.letterType:

            // @var string
            letterTypeReply: 'reply',

            // @var string
            letterTypeReplyAll: 'replyAll',

            // @var string
            letterTypeNew: 'new',

            // @var string
            letterTypeForward: 'forward',

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

            // @var integer
            draftToEditEmailId: undefined,

            // @var string, one of 'screenXXX' literals
            currentScreen:undefined,

            // @var SkWindow
            windowObject:undefined,

            // @var SKEmail
            activeEmail:undefined,

            // @var string
            activeScreen:undefined,

            /** @var List.<SKMailFolder> */
            folders: {},

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
                try {
                    this.folders[this.aliasFolderInbox] = new SKMailFolder();
                    this.folders[this.aliasFolderInbox].alias = this.aliasFolderInbox;

                    this.folders[this.aliasFolderDrafts] = new SKMailFolder();
                    this.folders[this.aliasFolderDrafts].alias = this.aliasFolderDrafts;

                    this.folders[this.aliasFolderSended] = new SKMailFolder();
                    this.folders[this.aliasFolderSended].alias = this.aliasFolderSended;

                    this.folders[this.aliasFolderTrash] = new SKMailFolder();
                    this.folders[this.aliasFolderTrash].alias = this.aliasFolderTrash;
                    this.emailUIDs = {};
                    // init folder names
                    this.getInboxFolder().name  = 'Входящие';
                    this.getDraftsFolder().name = 'Черновики';
                    this.getSendedFolder().name = 'Исходящие';
                    this.getTrashFolder().name  = 'Корзина';
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @return string,
             */
            getActiveSubscreenName:function () {
                try {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }

                return 'mailMain';
            },

            /**
             * @method
             * @param id
             * @returns {*}
             */
            getMailTaskByMySqlId:function (id) {
                try {
                    for (var i in this.availaleActiveEmailTasks) {
                        if (parseInt(this.availaleActiveEmailTasks[i].mySqlId, 10) === parseInt(id,10)) {
                            return this.availaleActiveEmailTasks[i];
                        }
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }

                return undefined;
            },

            /**
             * @method
             * @return SkMailFolder
             */
            getInboxFolder:function () {
                try {
                    return this.folders[this.aliasFolderInbox];
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @return SkMailFolder
             */
            getDraftsFolder:function () {
                try {
                    return this.folders[this.aliasFolderDrafts];
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @return SkMailFolder
             */

            getSendedFolder:function () {
                try {
                    return this.folders[this.aliasFolderSended];
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @return SkMailFolder
             */
            getTrashFolder:function () {
                try {
                    return this.folders[this.aliasFolderTrash];
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
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
                try {
                    var me = this;
                    this.folders[folderAlias].emails = [];

                    _.forEach(emailsData, function(emailData) {
                        var subject                = new SKMailSubject();
                        subject.text               = emailData.subject;
                        subject.id                 = emailData.subjectId;
                        subject.characterSubjectId = emailData.subjectId;

                        var email               = new SKEmail();
                        email.folderAlias       = folderAlias;
                        email.letterType        = emailData.letterType;
                        email.mySqlId           = emailData.id;
                        email.text              = emailData.text;
                        email.is_readed         = (1 === parseInt(emailData.readed, 10));
                        email.is_has_attachment = (1 === parseInt(emailData.attachments, 10));
                        email.sendedAt          = emailData.sentAt;
                        email.subject           = subject;
                        email.phrases           = emailData.phraseOrder || [];
                        email.setSenderEmailAndNameStrings(emailData.sender);

                        var attachment = new SKAttachment();
                        attachment.label       = emailData.attachmentName;
                        attachment.fileMySqlId = emailData.attachmentFileId;

                        email.attachment = attachment;

                        var recipiens = emailData.receiver.split(',');
                        $.each(recipiens, function(index){
                            email.addRecipientEmailAndNameStrings(recipiens[index]);
                        });

                        if (emailData.copy !== undefined) {
                            var copies = emailData.copy.split(',');
                            $.each(copies, function(index){
                                email.addCopyEmailAndNameStrings(copies[index]);
                            });
                        }

                        if (undefined !== emailData.reply) {
                            email.previouseEmailText = emailData.reply;
                        }

                        me.folders[folderAlias].emails.push(email);
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param string activeScreenAlias, this.screenXxx literals
             */
            setActiveScreen:function (activeScreenAlias) {
                try {
                    this.activeScreen = activeScreenAlias;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @return: $.xhr array, Skiliks API responce
             */
            getDataForReplyToActiveEmail:function (cb) {
                try {
                    var mailClient = this;

                    return SKApp.server.api(
                        'mail/reply',
                        {
                            id:mailClient.activeEmail.mySqlId
                        },
                        function (response) {
                            if (1 == response.result) {
                                cb(response);
                            } else {
                                throw new Error("Can`t initialize responce email. Model. #1");
                            }
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @return: $.xhr array, Skiliks API responce
             */
            getDataForReplyAllToActiveEmail:function (cb) {
                try {
                    var mailClient = this;

                    return SKApp.server.api(
                        'mail/replyAll',
                        {
                            id:mailClient.activeEmail.mySqlId
                        },
                        function (response) {
                            if (1 === response.result) {
                                cb(response);
                            } else {
                                throw new Error("Can`t initialize responce email. Model. #2");
                            }
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @returns {$.xhr}
             */
            getDataForForwardActiveEmail:function (cb) {
                try {
                    var mailClient = this;

                    return SKApp.server.api(
                        'mail/forward',
                        {
                            id:mailClient.activeEmail.mySqlId
                        },
                        function (response) {
                            if (1 === response.result) {
                                return cb(response);
                            } else {
                                throw new Error("Can`t initialize responce email. Model. #3");
                            }
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            getDataForInitialScreen:function () {
                try {
                    var me = this;
                    // mark INCOME folders as active
                    this.folders[this.aliasFolderInbox].isActive = true;
                    var folder_to_load = 4;
                    var onSent = function () {
                        folder_to_load -= 1;
                        if (folder_to_load === 0) {
                            me.trigger('init_completed');
                        }
                        return folder_to_load;
                    };

                    this.getInboxFolderEmails(onSent);
                    this.getDraftsFolderEmails(onSent);
                    this.getSendedFolderEmails(onSent);
                    this.getTrashFolderEmails(onSent);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            // todo: combine all getXxxFolderEmails() to one method.

            /**
             * @method
             * @param cb
             */
            getInboxFolderEmails:function (cb) {
                try {
                    var me = this;
                    SKApp.server.api(
                        'mail/getMessages',
                        {
                            folderId:me.codeFolderInbox,
                            order:'time',
                            order_type:1
                        },
                        function (responce) {
                            me.updateInboxFolderEmails(responce.messages);
                            if (undefined !== cb) {
                                cb();
                            }
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param cb
             */
            getDraftsFolderEmails:function (cb) {
                try {
                    SKApp.server.api(
                        'mail/getMessages',
                        {
                            folderId:SKApp.simulation.mailClient.codeFolderDrafts,
                            order:'time',
                            order_type:1
                        },
                        function (responce) {
                            SKApp.simulation.mailClient.updateDraftsFolderEmails(responce.messages);
                            if (undefined !== cb) {
                                cb();
                            }
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param cb
             */
            getSendedFolderEmails:function (cb) {
                try {
                    var MailClientModel = this;
                    SKApp.server.api(
                        'mail/getMessages',
                        {
                            folderId:   MailClientModel.codeFolderSended,
                            order:      'time',
                            order_type: 1
                        },
                        function (responce) {
                            MailClientModel.updateSendedFolderEmails(responce.messages);
                            if (undefined !== cb) {
                                cb();
                            }
                            MailClientModel.trigger('outbox:updated');
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param cb
             */
            getTrashFolderEmails:function (cb) {
                try {
                    SKApp.server.api(
                        'mail/getMessages',
                        {
                            folderId:SKApp.simulation.mailClient.codeFolderTrash,
                            order:'time',
                            order_type:1
                        },
                        function (responce) {
                            SKApp.simulation.mailClient.updateTrashFolderEmails(responce.messages);
                            if (undefined !== cb) {
                                cb();
                            }
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            // todo: combine all updateXxxFolderEmails() to one method.

            /**
             * @method
             * @param messages
             */
            updateInboxFolderEmails:function (messages) {
                try {
                    this.setEmailsToFolder(this.aliasFolderInbox, messages);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param messages
             */
            updateDraftsFolderEmails:function (messages) {
                try {
                    this.setEmailsToFolder(this.aliasFolderDrafts, messages);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param messages
             */
            updateSendedFolderEmails:function (messages) {
                try {
                    this.setEmailsToFolder(this.aliasFolderSended, messages);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param messages
             */
            updateTrashFolderEmails:function (messages) {
                try {
                    this.setEmailsToFolder(this.aliasFolderTrash, messages);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @returns {*}
             */
            getActiveEmailId:function () {
                try {
                    if (undefined === this.activeEmail) {
                        return undefined;
                    }

                    return this.activeEmail.mySqlId;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param email
             */
            setActiveEmail:function (email) {
                try {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param alias
             */
            setActiveFolder:function (alias) {
                try {
                    var me = this;
                    $.each(me.folders, function(i){
                        me.folders[i].isActive = false;
                        if (alias === i) {
                            me.folders[i].isActive = true;
                        }
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
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
                try {
                    for (var i in this.folders) {
                        if (this.folders[i].isActive) {
                            return this.folders[i];
                        }
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
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
                try {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @returns {*}
             */
            getSimulationMailClientWindow:function () {
                try {
                    var windows = SKApp.simulation.window_set.where({name:'mailEmulator'});

                    if (undefined === windows || 0 === windows.length) {
                        console.warn('There is no active window object for mailClient.');
                    }

                    return windows[0];
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param newSubscreen
             * @param {integer} emailId
             */
            setWindowsLog:function (newSubscreen, emailId) {
                try {
                    var window = this.getSimulationMailClientWindow();
                    var oldSubwindowKey, newSubwindowKey;
                    if (window.get('params') && window.get('params').mailId) {
                        oldSubwindowKey = window.get('subname')  + '/' + window.get('params').mailId;
                    }
                    if (emailId) {
                        newSubwindowKey = newSubscreen  + '/' + emailId;
                    }
                    window.setOnTop();
                    if (oldSubwindowKey && !this.emailUIDs[oldSubwindowKey]) {
                            this.emailUIDs[oldSubwindowKey] = window.window_uid;
                    }
                    SKApp.simulation.windowLog.deactivate(window);
                    if (newSubwindowKey) {
                        if (this.emailUIDs[newSubwindowKey]) {
                            window.window_uid = this.emailUIDs[newSubwindowKey];
                        } else {
                            window.updateUid();
                            this.emailUIDs[newSubwindowKey] = window.window_uid;
                        }
                    } else {
                        window.updateUid();
                    }

                    window.set('id', newSubscreen);
                    window.set('subname', newSubscreen);
                    window.set('params', {'mailId':emailId});

                    SKApp.simulation.windowLog.activate(window);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            setTaskId:function (planId) {
                try {
                    var mailWIndow = SKApp.simulation.window_set.where({name:'mailEmulator', id:'mailPlan'})[0];
                    if( mailWIndow !== undefined ) {
                        var params = mailWIndow.get('params');
                        params.planId = planId;
                        mailWIndow.set('params', params);
                    } else {
                        throw new Error("mailPlan not found");
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param attachmentId
             */
            saveAttachmentToMyDocuments:function (attachmentId) {
                try {
                    var me = this;

                    // call saveAttachment URL
                    SKApp.server.api(
                    'myDocuments/add',
                    {
                        attachmentId:attachmentId
                    },
                    function (response) {
                        // and display message for user
                        if (response.result === 1) {
                            if (response.status === true) {
                                AppView.frame.icon_view.doSoundSaveAttachment();
                            }

                            SKApp.simulation.documents.fetch();
                            SKApp.simulation.documents.once('afterReset', function() {
                                new SKDialogView({
                                    'message':'Файл был успешно сохранён в папку Мои документы.',
                                    'buttons':[
                                        {
                                            'value':'Ок'
                                        }
                                    ]
                                });

                                me.trigger('attachment:saved');
                            });
                        } else {
                            throw new Error('Can not add document');
                        }
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param id
             * @returns {*}
             */
            getRecipientByMySqlId:function (id) {
                try {
                    return SKApp.simulation.characters.get(id);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param name
             * @returns {*}
             */
            getRecipientByName:function (name) {
                try {
                    for (var i in this.defaultRecipients) {
                        // keep non strict!
                        if (name == this.defaultRecipients[i].get('fio')) {
                            return this.defaultRecipients[i];
                        }
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
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
                try {
                    return name && SKApp.simulation.characters.where({'fio':name})[0].get('id');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            updateRecipientsList:function (cb) {
                try {
                    var me = this;

                    me.defaultRecipients = [];
                    Array.prototype.push.apply(me.defaultRecipients, SKApp.simulation.characters.withoutHero());
                    if (cb !== undefined) {
                        cb();
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @returns {Array}
             */
            getFormatedCharacterList:function () {
                try {
                    var list = [];
                    for (var i in this.defaultRecipients) {
                        // non strict "!=" is important!
                        if(this.activeScreen !== 'SCREEN_WRITE_NEW_EMAIL'){
                            if ('' !== this.defaultRecipients[i].get('fio') && '' !== this.defaultRecipients[i].get('email')) {
                                list.push(this.defaultRecipients[i].getFormatedForMailToName());
                            }
                        }else{
                            if ('' !== this.defaultRecipients[i].get('fio') && '' !== this.defaultRecipients[i].get('email') && parseInt(this.defaultRecipients[i].get('has_mail_theme')) === 1) {
                                list.push(this.defaultRecipients[i].getFormatedForMailToName());
                            }
                        }


                    }

                    return list;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param {Array.<integer>} recipientIds
             * @param action
             * @param {undefined|SKMailSubject} parent_subject
             */
            reloadSubjectsWithWarning:function (recipientIds, action, parent_subject, callback, el_tag, updateSubject) {
                try {
                    var mailClient = this;

                    // display warning only if user add extra recipients
                    if (0 !== mailClient.availablePhrases.length && _.first(recipientIds) === mailClient.findRecipientByName($(el_tag).text().replace(/\s\((.*)\)/g, '')) &&  this.isNotEmptySubject()) {
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
                        return true;
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param recipientIds
             * @param subject
             */
            reloadSubjects:function (recipientIds, subject, callback) {
                try {
                    if(recipientIds.length <= 0) {
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

                                // clean up phrases {
                                if (SKApp.simulation.mailClient.activeEmail) {
                                    SKApp.simulation.mailClient.activeEmail.phrases = [];
                                }
                                // clean up phrases }

                                for (var i in response.data) {
                                    var string = response.data[i];

                                    var subject = new SKMailSubject();
                                    subject.characterSubjectId = i;
                                    subject.text = response.data[i];

                                    SKApp.simulation.mailClient.availableSubjects.push(subject);
                                }
                                if(typeof callback == 'function'){
                                    callback();
                                }
                                me.trigger('mail:subject_list_in_model_updated');
                            }
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param array
             */
            setRegularAvailablePhrases:function (array) {
                try {
                    this.messageForNewEmail = '';
                    this.availablePhrases = []; // clean-up old phrases

                    for (var i in array) {

                        var phrase = new SKMailPhrase();
                        phrase.mySqlId = parseInt(i);
                        phrase.text = array[i].name;
                        phrase.columnNumber = parseInt(array[i].column_number);
                        this.availablePhrases.push(phrase);
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param array
             */
            setAdditionalAvailablePhrases:function (array) {
                try {
                    this.availableAdditionalPhrases = []; // clean-up old phrases

                    for (var i in array) {
                        var phrase = new SKMailPhrase();
                        phrase.mySqlId = parseInt(i);
                        phrase.text = array[i].name;
                        phrase.columnNumber = parseInt(array[i].column_number);
                        this.availableAdditionalPhrases.push(phrase);
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
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
                try {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Search throw list of avalibabte to add to email text phrases
             *
             * @method
             */
            getAvailablePhraseByMySqlId:function (phraseId) {
                try {
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
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
                try {
                    var phrases = this.newEmailUsedPhrases;
                    for (var i in phrases) {
                        // keep '==' not strict!
                        if (phrases[i].uid == phraseUid) {
                            return phrases[i];
                        }
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }

                return undefined;
            },

            /**
             * @method
             * @param cb
             */
            uploadAttachmentsList:function (cb) {
                try {
                    var callback = cb;
                    SKApp.server.api(
                        'myDocuments/getList',
                        {},
                        function (response) {
                            if (undefined !== response.data) {
                                SKApp.simulation.mailClient.availableAttachments = [];
                                for (var i in response.data) {

                                    var attach = new SKAttachment();
                                    attach.fileMySqlId = response.data[i].id;
                                    attach.label = response.data[i].name;

                                    SKApp.simulation.mailClient.availableAttachments.push(attach);
                                }
                                callback();
                            }
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param id
             * @returns {*}
             */
            getCharacterById:function (id) {
                try {
                    return SKApp.simulation.characters.get(id);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * What is it?
             *
             * @method
             * @param emailToSave
             * @return {Object}
             */
            combineMailDataByEmailObject:function (emailToSave) {
                try {
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
                        id:         emailToSave.mySqlId,
                        copies:     emailToSave.getCopyToIdsString(),
                        fileId:     emailToSave.getAttachmentId(),
                        messageId:  mailId,
                        phrases:    emailToSave.getPhrasesIdsString(),
                        receivers:  emailToSave.getRecipientIdsString(),
                        subject:    emailToSave.subject.characterSubjectId,
                        letterType: type
                    };
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param emailToSave
             * @param callback
             * @returns {boolean}
             */
            sendNewCustomEmail:function (emailToSave, callback) {
                try {
                    var me = this;
                    if (false === this.validationDialogResult(emailToSave)) {
                        return false;
                    }

                    this.trigger('process:start');
                    SKApp.server.api(
                        'mail/sendMessage',
                        this.combineMailDataByEmailObject(emailToSave),
                        function (response) {
                            var windows = SKApp.simulation.window_set.where({name: 'mailEmulator'});
                            windows[0].setOnTop();
                            if (1 === response.result) {
                                var window = me.getSimulationMailClientWindow();
                                window.set('params', {'mailId': response.messageId});
                                me.trigger('mail:sent');
                                me.getSendedFolderEmails(function () {
                                    if (callback !== undefined) {
                                        callback();
                                    }
                                    me.trigger('process:finish');
                                }); // callback is usually 'render active folder'
                            } else {
                                me.trigger('process:finish');
                                me.message_window =
                                    me.message_window || new SKDialogView({
                                        'message':'Не удалось отправить письмо.',
                                        'buttons':[
                                            {
                                                'value':'Ок',
                                                'onclick':function () {
                                                    delete SKApp.simulation.mailClient.message_window;
                                                }
                                            }
                                        ]
                                    });
                            }
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param emailToSave
             * @returns {boolean}
             */
            validationDialogResult:function (emailToSave) {
                try {
                    var mailClient = this;

                    // validation {
                    // email.recipients
                    if (0 === emailToSave.recipients.length) {
                        mailClient.message_window = new SKDialogView({
                            'message':'Добавьте адресата письма.',
                            'buttons':[
                                {
                                    'value':'Ок',
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
                                    'value':'Ок',
                                    'onclick':function () {
                                        delete mailClient.message_window;
                                    }
                                }
                            ]
                        });
                        return false;
                    }
                    // validation }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }

                return true;
            },

            /**
             * @method
             * @param emailToSave
             * @param cb
             * @returns {boolean}
             */
            saveToDraftsEmail:function (emailToSave, cb) {
                try {
                    var mailClient = this;

                    if (false === this.validationDialogResult(emailToSave)) {
                        return false;
                    }

                    this.trigger('process:start');
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
                                            'value':'Ок',
                                            'onclick':function () {
                                                delete mailClient.message_window;
                                            }
                                        }
                                    ]
                                });
                            }

                            mailClient.draftToEditEmailId = undefined;
                            mailClient.trigger('process:finish');
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }

                return true;
            },

            /**
             * Отправляет письмо "фантастическим образом" — открывается почтовый клиент с написанным письмом и письмо отправляется
             * @param email
             * @method
             */
            'sendFantasticMail': function (email) {
                /**
                 * @param {SKEmail} email
                 * @event mail:fantastic-send
                 */
                try {
                    this.trigger('mail:fantastic-send', email);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Открывает письмо "фантастическим образом" — открывается почтовый клиент открытым письмом
             * @param email
             * @method
             */
            'openFantasticMail': function (email) {
                /**
                 * @param {SKEmail} email
                 * @event mail:fantastic-send
                 */
                try {
                    this.trigger('mail:fantastic-open', email);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            openWindow: function () {
                try {
                    this.getDataForInitialScreen();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * To rewrite
             * @method
             * @return {Boolean}
             */
            isNotEmptySubject:function(){
                try {
                    return $("#MailClient_NewLetterSubject").data('ddslick').selectedData !== null &&
                        $("#MailClient_NewLetterSubject").data('ddslick').selectedData.value !== 0;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });
    return SKMailClient;
});

