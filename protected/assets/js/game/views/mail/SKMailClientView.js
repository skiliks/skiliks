/*global Backbone, _, SKApp, SKAttachment, SKMailSubject, define, console, $, SKMailPhrase,
SKDocumentsWindow, SKMailPhrase */
var SKMailClientView;

define([
    "game/views/SKDialogView",
    "game/views/SKWindowView",
    "game/views/mail/SKMailAddToPlanDialog",
    "game/models/SKEmail",
    "game/models/SKAttachment",


    "text!game/jst/mail/title.jst",
    "text!game/jst/mail/content.jst",
    "text!game/jst/mail/folder_label.jst",

    "text!game/jst/mail/income_line.jst",
    "text!game/jst/mail/income_folder_skeleton.jst",
    "text!game/jst/mail/action.jst",

    "text!game/jst/mail/preview.jst",
    "text!game/jst/mail/new_email.jst",
    "text!game/jst/mail/phrase.jst",

    "text!game/jst/mail/sended_folder_sceleton.jst",
    "text!game/jst/mail/send_mail_line.jst",
    "text!game/jst/mail/read_email_sceleton.jst",
    "text!game/jst/mail/trash_email_line.jst",

    "text!game/jst/mail/trash_folder_sceleton.jst",
    "text!game/jst/mail/phrase_item.jst"

], function (SKDialogView, SKWindowView, SKMailAddToPlanDialog, SKEmail, SKAttachment,

    mail_client_title_template, mail_client_content_template, folder_label_template,
    mail_client_income_line_template, income_folder_skeleton_template, mail_client_action_template,
    mail_client_email_preview_template, mail_client_new_email_template, mail_client_phrase_template,
    mail_sender_folder_sceleton_template, send_mail_line, read_mail_sceleton, trash_email_line,
    trash_folder_sceleton, phrase_item

) {
    "use strict";
    /**
     * @class SKMailClientView
     * @augments Backbone.View
     */
    SKMailClientView = SKWindowView.extend(
        /** @lends SKMailClientView.prototype */
        {
            isDisplaySettingsButton:true,
            dimensions: {
                /*maxWidth: 1100,
                maxHeight: 700*/
            },
            mailClient: undefined,

            windowName:'mail',

            addClass: 'mail-window',

            addId: 'mail-window',

            mailClientScreenID: 'mailEmulatorMainScreen',

            mailClientFoldersListId: 'MailClient_FolderLabels',

            mailClientContentBlockId: 'MailClient_ContentBlock',

            mailClientIncomeFolderListId: 'MailClient_IncomeFolder_List',

            mailClientInboxFolderEmailPreviewId: 'MailClient_IncomeFolder_EmailPreview',

            mailClientReadEmailContentBoxId: 'MailClient_ReadEmail_Content',

            currentRecipients : [],

            // used to indicate is jQuery table sorter applied
            // .tablesorter increase internal array avery bind,
            // but hasn`t internal method to check is it binded to element or not
            isSortingNotApplied: true,

            parentSubject: undefined,

            /**
             * События DOM на которые должна реагировать данная view
             * @var Array events
             */
            events: _.defaults({
                'click .NEW_EMAIL': 'renderWriteCustomNewEmailScreen',
                'click .REPLY_EMAIL': 'renderReplyScreen',
                'click .REPLY_ALL_EMAIL': 'renderReplyAllScreen',
                'click .FORWARD_EMAIL': 'renderForwardEmailScreen',
                'click .ADD_TO_PLAN': 'doAddToPlan',
                'click .SAVE_TO_DRAFTS': 'doSaveEmailToDrafts',
                'click .SEND_EMAIL': 'doSendEmail',
                'click .MOVE_TO_TRASH': 'doMoveToTrashActiveEmail',
                'click .RESTORE': 'doMoveToInboxByClick',
                'click #FOLDER_INBOX': 'doRenderFolderByEvent',
                'click #FOLDER_DRAFTS': 'doRenderFolderByEvent',
                'click #FOLDER_SENDED': 'doRenderFolderByEvent',
                'click #FOLDER_TRASH': 'doRenderFolderByEvent',

                'click .SEND_DRAFT_EMAIL': 'doSendDraft',
                'click .save-attachment-icon': 'doSaveAttachment',
                'click #mailEmulatorNewLetterText li': 'doRemovePhraseFromEmail',
                'click #MailClient_ContentBlock .mail-tags-bl td':    'doAddPhraseToEmail',
                'click .switch-size': 'doSwitchNewLetterView'
            }, SKWindowView.prototype.events),

            /**
             * Constructor
             * @method initialize
             */
            initialize: function () {
                try {
                    if (window.Raven) {
                        window.Raven.captureMessage(
                            'Log. Open mail client window. ' + SKApp.simulation.getGameTime({with_seconds: true})
                        );
                    }
                    var me = this;

                    this.isFantasticSend = false; // indicate is email send in fantastic way

                    this.mailClient = SKApp.simulation.mailClient;
                    this.mailClient.view = this;

                    // init RecipientsList
                    if (0 === this.mailClient.defaultRecipients.length) {
                        this.mailClient.updateRecipientsList();
                    }

                    // init View according model
                    this.listenTo(this.mailClient, 'init_completed', function () {
                        try {
                            me.doRenderFolder(me.mailClient.aliasFolderInbox, true, true);
                            me.trigger('render_finished');
                            me.render_finished = true;

                            var window = me.mailClient.getSimulationMailClientWindow();
                            me.mailClient.window_uid = parseInt(window.window_uid, 10);
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });

                    // render character subjects list
                    this.listenTo(this.mailClient, 'mail:subject_list_in_model_updated', function () {
                        try {
                            if(me.$("#MailClient_RecipientsList li.tagItem").get().length === 0){
                                me.mailClient.availablePhrases = [];
                                me.mailClient.availableAdditionalPhrases = [];
                                me.mailClient.availableSubjects = [];
                            }else{
                                me.updateSubjectsList();
                                me.mailClient.availablePhrases = [];
                                me.mailClient.availableAdditionalPhrases = [];
                                me.renderPhrases();
                            }
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });

                    // render phrases
                    this.listenTo(this.mailClient, 'mail:available_phrases_reloaded', function () {
                        try {
                            me.renderPhrases();
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });

                    // update inbox emails counter
                    this.listenTo(this.mailClient, 'mail:update_inbox_counter', function () {
                        try {
                            var unreaded = me.mailClient.getInboxFolder().countUnreaded();
                            me.updateMailIconCounter(unreaded);
                            me.updateInboxFolderCounter(unreaded);
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });

                    this.listenTo(this.mailClient, 'outbox:updated', this.onMailOutboxUpdated);
                    this.listenTo(this.mailClient, 'mail:sent', this.onMailSent);
                    this.listenTo(this.mailClient, 'mail:fantastic-send', this.onMailFantasticSend);
                    this.listenTo(this.mailClient, 'mail:fantastic-open', this.onMailFantasticOpen);
                    // close with conditions action {
                    this.listenTo(this.options.model_instance, 'pre_close', this.onBeforeClose);
                    // close with conditions action }

                    this.listenTo(this.mailClient, 'process:start', this.onMailProcessStart);
                    this.listenTo(this.mailClient, 'process:finish', this.onMailProcessEnd);

                    // call parent initialize();
                    SKWindowView.prototype.initialize.call(this);

                    this.listenTo(SKApp.simulation.events, 'event:mail', _.bind(function() {
                        try {
                            var callback = function() {
                                try {
                                    me.updateFolderLabels();
                                    if (me.mailClient.getActiveFolder().alias === me.mailClient.aliasFolderInbox &&
                                        (
                                            me.mailClient.activeScreen === me.mailClient.screenInboxList ||
                                                me.mailClient.activeScreen === me.mailClient.screenDraftsList ||
                                                me.mailClient.activeScreen === me.mailClient.screenSendedList ||
                                                me.mailClient.activeScreen === me.mailClient.screenTrashList
                                            )) {

                                        var isSwitchToFirstEmail = false;
                                        if (undefined === me.mailClient.activeEmail || null === undefined === me.mailClient.activeEmail) {
                                            isSwitchToFirstEmail = true;
                                        }

                                        me.doRenderFolder(me.mailClient.aliasFolderInbox, isSwitchToFirstEmail, true);
                                    }
                                    me.mailClient.trigger('mail:update_inbox_counter');
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            };

                            me.mailClient.getInboxFolderEmails(callback);
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    }, me));
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            /**
             * Вызывается перед закрытием почтового окна. Предлагает сохранить черновик
             * @method onBeforeClose
             */
            'onBeforeClose': function () {
                try {
                    var me = this;
                    me.options.model_instance.prevent_close = !me.isCanBeClosed();

                    if (false === me.isCanBeClosed()) {
                        var mailClient = me.mailClient;
                        var mailClientView = me;

                        mailClientView.options.model_instance.prevent_close = true;

                        mailClient.message_window = new SKDialogView({
                            'message': 'Сохранить письмо в черновиках?',
                            'buttons': [
                                {
                                    'value': 'Не сохранять',
                                    'onclick': function () {
                                        try {
                                            mailClientView.renderActiveFolder();
                                        } catch(exception) {
                                            if (window.Raven) {
                                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                            }
                                        }
                                    }
                                },
                                {
                                    'value': 'Отмена',
                                    'onclick': function () {
                                        try {
                                            delete mailClient.message_window;
                                        } catch(exception) {
                                            if (window.Raven) {
                                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                            }
                                        }
                                    }
                                },
                                {
                                    'value': 'Сохранить',
                                    'onclick': function () {
                                        try {
                                            mailClientView.doSaveEmailToDrafts();
                                        } catch(exception) {
                                            if (window.Raven) {
                                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                            }
                                        }
                                    }
                                }
                            ]
                        });
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            /**
             * @method
             * @param event
             */
            doSwitchNewLetterView: function (event) {
                try {
                    if ($(event.currentTarget).hasClass('min')) {
                        // maximize {
                        $(event.currentTarget).removeClass('min');
                        $(event.currentTarget).addClass('max');

                        $('.mail-view-header').addClass('min');
                        $('.mail-new-text').addClass('max');
                        $('.mail-new-text-scroll').addClass('max');
                        $('.mail-text-wrap').addClass('mail-text-wrap-max');

                        $('.mail-text-area').css('height', 'calc(100% - 236px)');
                        // maximize }
                    } else {
                        // minimize {
                        $(event.currentTarget).removeClass('max');
                        $(event.currentTarget).addClass('min');

                        $('.mail-view-header').removeClass('min');
                        $('.mail-new-text').removeClass('max');
                        $('.mail-new-text-scroll').removeClass('max');
                        $('.mail-text-wrap').removeClass('mail-text-wrap-max');

                        $('.mail-text-area').css('height', 'calc(100% - 426px)');
                        // minimize }
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param event
             */
            doSaveAttachment: function (event) {
                try {
                    if("true" !== $(event.currentTarget).attr('data-disabled')) {
                        $(event.currentTarget).attr('data-disabled', 'true');
                        this.mailClient.saveAttachmentToMyDocuments($(event.currentTarget).data('document-id'));
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            doAddToPlan: function () {
                try {
                    var me = this;

                    if (undefined === me.mailClient.activeEmail || me.addToPlanDialog) {
                        return;
                    }

                    me.addToPlanDialog = new SKMailAddToPlanDialog();
                    me.addToPlanDialog.render();
                    me.addToPlanDialog.once('close', function() {
                        try {
                            delete me.addToPlanDialog;
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
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
             * @param counter
             */
            updateMailIconCounter: function (counter) {
                try {
                    var counterElement = $('#icons_email span');

                    if (0 === counterElement.length) {
                        $('#icons_email').html('<span></span>');
                        counterElement = $('#icons_email span');
                    }

                    counterElement.text(counter);
                    if (0 === counter) {
                        counterElement.remove();
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param counter
             */
            updateInboxFolderCounter: function (counter) {
                try {
                    var el = $('.icon_' + this.mailClient.aliasFolderInbox + ' .counter');
                    if (counter !== 0) {
                        el.text('(' + counter + ')');
                    } else {
                        el.text('');
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @returns {boolean}
             */
            isCanBeClosed: function () {
                try {
                    return this.forcedClose || (this.mailClient.activeScreen !== this.mailClient.screenWriteNewCustomEmail &&
                        this.mailClient.activeScreen !== this.mailClient.screenWriteReply &&
                        this.mailClient.activeScreen !== this.mailClient.screenWriteReplyAll &&
                        this.mailClient.activeScreen !== this.mailClient.screenWriteForward);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            setForcedClosing: function() {
                try {
                    this.forcedClose = true;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Стандартный родительский метод
             */
            remove: function () {
                try {
                    SKWindowView.prototype.remove.call(this);
                    this.mailClient.setActiveScreen(undefined);
                    this.mailClient.view = null;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Стандартный родительский метод
             * @param {jQuery} el
             */
            renderTitle: function (el) {
                try {
                    el.html(_.template(mail_client_title_template, {
                        isDisplaySettingsButton:this.isDisplaySettingsButton,
                        windowName:this.windowName}));
                    // // this.delegateEvents();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Стандартный родительский метод
             * @param {jQuery} el
             */
            renderContent: function (el) {
                try {
                    var mailClientWindowBasicHtml = _.template(mail_client_content_template, {
                        id: this.mailClientScreenID,
                        contentBlockId: this.mailClientContentBlockId,
                        isDisplaySettingsButton:this.isDisplaySettingsButton,
                        windowName:this.windowName
                    });
                    // append to <body>
                    el.html(mailClientWindowBasicHtml);
                    this.mailClient.getDataForInitialScreen();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Used to get data for email preview
             * @todo move to model?
             *
             * @method
             */
            doGetEmailDetails: function (emailId, folderAlias) {
                try {
                    var me = this;
                    // do we have full data for current email ? {
                    var email = SKApp.simulation.mailClient.folders[folderAlias].getEmailByMySqlId(emailId);

                    if ('undefined' === typeof email) {
                        throw new Error ('Try to render not exists email ' + emailId + '.');
                    }

                    this.highlightActiveEmail(email);
                    this.mailClient.setActiveEmail(email);

                    // render preview
                    me.renderEmailPreviewScreen(
                        email,
                        me.mailClientInboxFolderEmailPreviewId,
                        '100%'
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
            highlightActiveEmail: function(email) {
                try {
                    var activeClass = ' mail-emulator-received-list-string-selected active',
                        row;

                    row = this.$('#' + this.mailClientIncomeFolderListId + ' .email-list-line')
                        .removeClass(activeClass)
                        .filter('[data-email-id=' + email.mySqlId + ']')
                        .addClass(activeClass);

                    if (!email.is_readed) {
                        row.removeClass(email.getIsReadCssClass());
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            updateFolderLabels: function () {
                try {
                    var html = '';
                    var mailClientView = this;

                    for (var alias in this.mailClient.folders) {
                        if (this.mailClient.folders.hasOwnProperty(alias)) {
                            var isActiveCssClass = '';
                            if (this.mailClient.folders[alias].isActive) {
                                isActiveCssClass = ' active ';
                            }

                            var counter = this.mailClient.folders[alias].emails.length;

                            if (alias === this.mailClient.aliasFolderInbox) {
                                counter = this.mailClient.getInboxFolder().countUnreaded();
                            }
                            var counterCss = 'display: inline-block;';
                            if (alias === this.mailClient.aliasFolderDrafts || alias === this.mailClient.aliasFolderSended || alias === this.mailClient.aliasFolderTrash) {
                                counterCss = 'display: none;';
                            }
                            if (counter === 0) {
                                counterCss = 'display: none;';
                            }

                            html += _.template(folder_label_template, {
                                label: this.mailClient.folders[alias].name,
                                isActiveCssClass: isActiveCssClass,
                                counter: counter,
                                counterCss: counterCss,
                                alias: alias
                            });
                        }
                    }

                    this.$('#' + this.mailClientFoldersListId).html(html);

                    // this.delegateEvents();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param event
             */
            doRenderFolderByEvent: function (event) {
                try {
                    // to named what is $(e.currentTarget).data('alias')
                    var folderAlias = $(event.currentTarget).data('alias');

                    this.doRenderFolder(folderAlias);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param folderAlias
             * @param isSwitchToFirst
             * @param isInitialRender
             */
            doRenderFolder: function (folderAlias, isSwitchToFirst, doWriteLog) {
                try {
                    var mailClientView = this;

                    if (undefined === isSwitchToFirst) {
                        isSwitchToFirst = true;
                    }

                    if (undefined === doWriteLog) {
                        doWriteLog = false;
                    }

                    // script will assign table sotder for new folder
                    this.isSortingNotApplied = true;

                    this.mailClient.setActiveFolder(folderAlias);

                    // clean up phrases when render folders
                    this.mailClient.newEmailUsedPhrases = [];

                    if (this.mailClient.aliasFolderInbox === folderAlias) {

                        this.renderInboxFolder();
                    }

                    if (this.mailClient.aliasFolderSended === folderAlias) {

                        this.renderSendFolder();
                    }

                    if (this.mailClient.aliasFolderDrafts === folderAlias) {

                        this.renderDraftsFolder();
                    }

                    if (this.mailClient.aliasFolderTrash === folderAlias) {

                        this.renderTrashFolder();
                    }

                    this.updateFolderLabels();

                    // initial render has it's own logging call
                    if (false === doWriteLog) {
                        mailClientView.mailClient.setWindowsLog(
                            'mailMain',
                            mailClientView.mailClient.getActiveEmailId()
                        );
                    }

                    this.trigger('render_folder_finished');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Renders Inbox folder content
             *
             * @method
             */
            updateInboxListView: function () {
                try {
                    var activeEmail = this.mailClient.activeEmail;
                    this.mailClient.activeEmail = undefined;

                    // generate emails list {
                    var me = this;
                    // We  use this 2 variables to separate emails to display unreaded emails first in list
                    var emailsList = '';
                    var incomingEmails = this.mailClient.folders[this.mailClient.aliasFolderInbox].emails; // to make code shorter

                    _.values(incomingEmails).forEach(function (incomingEmail) {
                        try {
                            // generate HTML by template
                            emailsList += _.template(mail_client_income_line_template, {

                                emailMySqlId: incomingEmail.mySqlId,
                                senderName: incomingEmail.senderNameString,
                                subject: incomingEmail.subject.text,
                                sendedAt: incomingEmail.sendedAt,
                                isHasAttachment: incomingEmail.getIsHasAttachment(),
                                isHasAttachmentCss: incomingEmail.getIsHasAttachmentCss(),
                                isReadCssClass: incomingEmail.getIsReadCssClass()
                            });
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }                    });

                    // add emails list
                    this.$('#' + this.mailClientIncomeFolderListId + ' table tbody').html(emailsList);

                    this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderInbox);
                    if(activeEmail !== undefined){
                        this.setActiveEmail(activeEmail);

                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            updateTrashListView: function () {
                try {
                    this.mailClient.activeEmail = undefined;
                    // generate emails list {

                    // We  use this 2 variables to separate emails to display unreaded emails first in list
                    var emailsList = '';
                    var trashEmails = this.mailClient.folders[this.mailClient.aliasFolderTrash].emails; // to make code shorter

                    trashEmails.forEach(function (email) {
                        try {
                            //for (var key in trashEmails) {
                            // generate HTML by template
                            emailsList += _.template(trash_email_line, {

                                emailMySqlId:       email.mySqlId,
                                senderName:         email.senderNameString,
                                subject:            email.subject.text,
                                sendedAt:           email.sendedAt,
                                isHasAttachment:    email.getIsHasAttachment(),
                                isHasAttachmentCss: email.getIsHasAttachmentCss(),
                                isReadCssClass: true
                            });
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });

                    // add emails list
                    $('#' + this.mailClientIncomeFolderListId + ' table tbody').html(emailsList);

                    this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderTrash);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            updateSendListView: function () {
                try {
                    // generate emails list {
                    this.mailClient.activeEmail = undefined;
                    // We  use this 2 variables to separate emails to display unreaded emails first in list
                    var emailsList = '';
                    var sendedEmails = this.mailClient.folders[this.mailClient.aliasFolderSended].emails; // to make code shorter

                    for (var key in sendedEmails) {
                        // generate HTML by template
                        emailsList += _.template(send_mail_line, {

                            emailMySqlId: sendedEmails[key].mySqlId,
                            recipientName: sendedEmails[key].getFormattedRecipientsString(),
                            subject: sendedEmails[key].subject.text,
                            sendedAt: sendedEmails[key].sendedAt,
                            isHasAttachment: sendedEmails[key].getIsHasAttachment(),
                            isHasAttachmentCss: sendedEmails[key].getIsHasAttachmentCss(),
                            isReadCssClass: true
                        });
                    }

                    // add emails list
                    $('#' + this.mailClientIncomeFolderListId + ' table tbody').html(emailsList);

                    this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderSended);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            updateDraftsListView: function () {
                try {
                    // generate emails list {
                    this.mailClient.activeEmail = undefined;
                    // We  use this 2 variables to separate emails to display unreaded emails first in list
                    var emailsList = '';
                    var draftEmails = this.mailClient.folders[this.mailClient.aliasFolderDrafts].emails; // to make code shorter

                    for (var key in draftEmails) {
                        // generate HTML by template
                        emailsList += _.template(send_mail_line, {

                            emailMySqlId: draftEmails[key].mySqlId,
                            recipientName: draftEmails[key].getFormattedRecipientsString(),
                            subject: draftEmails[key].subject.text,
                            sendedAt: draftEmails[key].sendedAt,
                            isHasAttachment: draftEmails[key].getIsHasAttachment(),
                            isHasAttachmentCss: draftEmails[key].getIsHasAttachmentCss(),
                            isReadCssClass: true
                        });
                    }

                    // add emails list
                    $('#' + this.mailClientIncomeFolderListId + ' table tbody').html(emailsList);

                    this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderDrafts);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param folderAlias
             */
            addClickAndDoubleClickBehaviour: function (folderAlias) {
                try {
                    var mailClientView = this,
                        folderId = this.mailClientIncomeFolderListId,
                        $table = mailClientView.$('#' + folderId + ' table');

                    // Todo — move to events dictionary (GuGu)
                    $('.email-list-line').click(function (event) {
                        try {
                            mailClientView.onEmailClick(event.currentTarget);
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });

                    // make table sortable
                    if (this.isSortingNotApplied &&
                        0 !== $table.find('tbody tr').length) {
                        // add tablesorter filter for ouy specific date format {
                        $.tablesorter.addParser({
                            id: "customDate",
                            is: function (s) {
                                try {
                                    //24.01.2006 01:30 would also be matched
                                    return /\d{1,2}.\d{1,2}.\d{1,4} \d{1,2}:\d{1,2}/.test(s);
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            },
                            format: function (s) {
                                try {
                                    s = s.match(/(\d+)\.(\d+)\.(\d+) (\d+):(\d+)/);

                                    return $.tablesorter.formatFloat(new Date(s[3], s[2] - 1, s[1], s[4], s[5], 0).getTime());
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            },
                            type: "numeric"
                        });
                        // add tablesorter filter for ouy specific date format }

                        // init table sorter
                        $table.tablesorter({
                            sortInitialOrder: 'desc',
                            sortList: [
                                [2, 1],
                                [0, 0]
                            ],
                            headers: {
                                2: {
                                    sorter: 'customDate'
                                }
                            }
                        });

                        // Hack that allows us do sorting of table rows
                        mailClientView.$('#' + folderId + ' .ml-header > *').click(function() {
                            try {
                                $table.find('th:eq(' + $(this).index() + ')').click();
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        });

                        // SKILIKS-4940 {
                        if ($.browser['mozilla'] == true && $.browser.version < 29) {
                            $('.folder-list').height($('#mail-window').height() - 315);
                        }
                        // SKILIKS-4940 }

                        setTimeout(function () {
                            try {
                                var list = mailClientView.$('#' + folderId + ' .ml-list');
                                if (!list.hasClass('mCustomScrollbar')) {
                                    list.mCustomScrollbar({autoDraggerLength:false, updateOnContentResize: true});
                                }
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        }, 0);

                        this.isSortingNotApplied = false; // we upply sorting, so let other see it
                    } else {
                        $('#' + this.mailClientIncomeFolderListId + ' table').trigger('update');
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            onEmailClick : function(currentTarget) {
                // update lod data {
                var mailClientView = this;
                // if user click on same email line twice - open read email screen
                // Do not change == to ===
                if (mailClientView.mailClient.activeEmail !== undefined && $(currentTarget).data().emailId == mailClientView.mailClient.activeEmail.mySqlId) {
                    var emailId = $(currentTarget).data().emailId;
                    var email = mailClientView.mailClient.getEmailByMySqlId(emailId);
                    if (email.isDraft()) {
                        SKApp.server.api(
                            'mail/edit',
                            {
                                id: emailId
                            },
                            function (response) {
                                try {
                                    mailClientView.mailClient.activeEmail = email;
                                    if (email.isNew()) {
                                        mailClientView.renderWriteCustomNewEmailScreen(
                                            null,
                                            mailClientView.mailClient.iconsForEditDraftDraftScreenArray,
                                            email
                                        );
                                        mailClientView.fillMessageWindow(
                                            response,
                                            mailClientView.mailClient.iconsForEditDraftDraftScreenArray,
                                            true
                                        );

                                        // делаем тему выбранной
                                        mailClientView.selectSubjectByValue(email.subject.themeId);

                                        // обновляем список фраз
                                        mailClientView.doUpdateMailPhrasesList(
                                            {'selectedData':{'value': email.subject.themeId}}
                                        );

                                        mailClientView.mailClient.setActiveScreen(mailClientView.mailClient.screenWriteNewCustomEmail);
                                        mailClientView.mailClient.setWindowsLog('mailNew', email.mySqlId);
                                    }

                                    if (email.isForward()) {
                                        mailClientView.doUpdateScreenFromForwardEmailData(response, email);
                                        mailClientView.fillMessageWindow(response, mailClientView.mailClient.iconsForEditDraftDraftScreenArray, true);

                                        // обновляем список фраз
                                        mailClientView.doUpdateMailPhrasesList(
                                            {'selectedData':{'value': email.subject.themeId}}
                                        );

                                        mailClientView.mailClient.setActiveScreen(mailClientView.mailClient.screenWriteForward);
                                        mailClientView.mailClient.setWindowsLog('mailNew', email.mySqlId);
                                    }

                                    if (email.isReply()) {
                                        mailClientView.fillMessageWindow(response, mailClientView.mailClient.iconsForEditDraftDraftScreenArray);

                                        // обновляем список фраз
                                        mailClientView.doUpdateMailPhrasesList(
                                            {'selectedData':{'value': email.subject.themeId}}
                                        );

                                        mailClientView.mailClient.setActiveScreen(mailClientView.mailClient.screenWriteReply);
                                        mailClientView.mailClient.setWindowsLog('mailNew', email.mySqlId);
                                    }

                                    if (email.isReplyAll()) {
                                        mailClientView.fillMessageWindow(response, mailClientView.mailClient.iconsForEditDraftDraftScreenArray);

                                        // обновляем список фраз
                                        mailClientView.doUpdateMailPhrasesList(
                                            {'selectedData':{'value': email.subject.themeId}}
                                        );

                                        mailClientView.mailClient.setActiveScreen(mailClientView.mailClient.screenWriteReplyAll);
                                        mailClientView.mailClient.setWindowsLog('mailNew', email.mySqlId);
                                    }
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            }
                        );
                    } else {
                        // log {
                        mailClientView.mailClient.setWindowsLog(
                            'mailPreview',
                            $(currentTarget).data().emailId
                        );
                        // log }

                        mailClientView.renderReadEmail(
                            mailClientView.mailClient.getEmailByMySqlId($(currentTarget).data().emailId)
                        );
                        mailClientView.mailClient.setActiveScreen(mailClientView.mailClient.screenReadEmail);
                    }
                } else {
                    // if user clicks on different email lines - activate clicked line email
                    // log {
                    mailClientView.mailClient.setWindowsLog(
                        'mailMain',
                        $(currentTarget).data().emailId
                    );
                    // log }
                    mailClientView.doGetEmailDetails(
                        $(currentTarget).data().emailId,
                        this.mailClient.getActiveFolder().alias
                    );
                }
            },

            /**
             * Renders current folder
             *
             * @method
             */
            renderActiveFolder: function () {
                try {
                    var mailClientView = this;
                    mailClientView.doRenderFolder(mailClientView.mailClient.getActiveFolder().alias);
                    mailClientView.mailClient.draftToEditEmailId = undefined;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            renderInboxFolder: function () {
                try {
                    this.unhideFoldersBlock();
                    // set HTML skeleton {
                    var skeleton = _.template(income_folder_skeleton_template, {
                        listId: this.mailClientIncomeFolderListId,
                        emailPreviewId: this.mailClientInboxFolderEmailPreviewId
                    });

                    this.$('#' + this.mailClientContentBlockId).html(skeleton);
                    // set HTML skeleton }

                    this.updateInboxListView();

                    // set icons {
                    var icons = this.mailClient.iconsForInboxScreenArray;
                    if (SKApp.isTutorial()) {
                        icons = this.mailClient.iconsForTutorialScenarioFolderInbox;
                    }
                    this.renderIcons(icons);
                    // set icons }

                    this.mailClient.setActiveScreen(this.mailClient.screenInboxList);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            renderTrashFolder: function () {
                try {
                    this.unhideFoldersBlock();

                    // set HTML skeleton {
                    var skeleton = _.template(trash_folder_sceleton, {
                        listId: this.mailClientIncomeFolderListId,
                        emailPreviewId: this.mailClientInboxFolderEmailPreviewId
                    });

                    this.$('#' + this.mailClientContentBlockId).html(skeleton);
                    // set HTML skeleton }

                    this.updateTrashListView();
                    // set icons {
                    var icons = this.mailClient.iconsForTrashScreenArray;
                    if (SKApp.isTutorial()) {
                        icons = this.mailClient.iconsForTutorialScenarioFolderTrash;
                    }
                    this.renderIcons(icons);
                    // set icons }

                    this.mailClient.setActiveScreen(this.mailClient.screenTrashList);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            renderSendFolder: function () {
                try {
                    this.unhideFoldersBlock();

                    // set HTML skeleton {
                    var skeleton = _.template(mail_sender_folder_sceleton_template, {
                        listId: this.mailClientIncomeFolderListId,
                        emailPreviewId: this.mailClientInboxFolderEmailPreviewId
                    });

                    this.$('#' + this.mailClientContentBlockId).html(skeleton);
                    // set HTML skeleton }

                    this.updateSendListView();

                    // set icons {
                    var icons = this.mailClient.iconsForSendedScreenArray;
                    if (SKApp.isTutorial()) {
                        icons = this.mailClient.iconsForTutorialScenarioFolderSend;
                    }
                    this.renderIcons(icons);
                    // set icons }

                    // this dublicates model code, but this is first step to use models like data storage only

                    this.updateFolderLabels();

                    this.mailClient.setActiveScreen(this.mailClient.screenSendedList);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            renderDraftsFolder: function () {
                try {
                    this.unhideFoldersBlock();

                    // set HTML skeleton {
                    var skeleton = _.template(mail_sender_folder_sceleton_template, {
                        listId: this.mailClientIncomeFolderListId,
                        emailPreviewId: this.mailClientInboxFolderEmailPreviewId
                    });

                    this.$('#' + this.mailClientContentBlockId).html(skeleton);
                    // set HTML skeleton }

                    this.updateDraftsListView();

                    // set icons {
                    var icons = this.mailClient.iconsForDraftsScreenArray;
                    if (SKApp.isTutorial()) {
                        icons = this.mailClient.iconsForTutorialScenarioFolderDrafts;
                    }
                    this.renderIcons(icons);
                    // set icons }

                    // this dublicates model code, but this is first step to use models like data storage only

                    this.updateFolderLabels();

                    this.mailClient.setActiveScreen(this.mailClient.screenDraftsList);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param email
             * @param id
             * @param height
             */
            renderEmailPreviewScreen: function (email, id, height) {
                try {
                    this.mailClient.setActiveEmail(email);

                    var attachmentLabel = '';
                    var attachmentId = '';
                    if (undefined !== email.attachment) {
                        attachmentLabel = email.attachment.label;
                        attachmentId = email.attachment.fileMySqlId;
                    }

                    var emailPreviewTemplate = _.template(mail_client_email_preview_template, {
                        emailMySqlId: email.mySqlId,
                        senderName: email.senderNameString,
                        recipientName: email.recipientNameString, //this.mailClient.heroNameEmail,
                        copyNamesLine: email.copyToString,
                        subject: email.subject.text,
                        text: email.text,
                        sendedAt: email.sendedAt,
                        isHasAttachmentCss: email.getIsHasAttachmentCss(),
                        isReadCssClass: email.getIsReadCssClass(),
                        attachmentFileName: attachmentLabel,
                        attachmentId: attachmentId,
                        height: height
                    });

                    this.$('#' + id).html(emailPreviewTemplate);

                    this.renderPreviousMessage(email.previousEmailText);
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
            renderReadEmail: function (email) {
                try {
                    // set HTML skeleton {
                    var skeleton = _.template(read_mail_sceleton, {
                        emailPreviewId: this.mailClientReadEmailContentBoxId
                    });

                    $('#' + this.mailClientContentBlockId).html(skeleton);
                    // set HTML skeleton}
                    this.renderEmailPreviewScreen(email, this.mailClientReadEmailContentBoxId, '350px');
                    this.mailClient.setActiveScreen(this.mailClient.screenReadEmail);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param iconButtonAliaces
             */
            renderIcons: function (iconButtonAliaces) {
                try {
                    var me = this;
                    // set defaults {
                    var iconsListHtml = '';

                    var addButtonNewEmail = false;
                    var addButtonReply = false;
                    var addButtonReplyAll = false;
                    var addButtonForward = false;
                    var addButtonAddToPlan = false;
                    var addButtonSend = false;
                    var addButtonSaveDraft = false;
                    var addButtonSendDraft = false;
                    var addButtonMoveToTrash = false;
                    var addButtonRestore = false;
                    // set defaults }

                    // choose icons to show {
                    iconButtonAliaces.forEach(function (alias) {
                        try {
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
                                case me.mailClient.aliasButtonRestore:
                                    addButtonRestore = true;
                                    break;
                            }
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });
                    // choose icons to show }

                    // conpose HTML code {
                    // declarate action_icon just avoid long strings
                    var action_icon = mail_client_action_template;

                    if (addButtonAddToPlan) {
                        iconsListHtml += _.template(action_icon, {
                            iconCssClass: this.mailClient.aliasButtonAddToPlan,
                            label: 'запланировать'
                        });
                    }
                    if (addButtonNewEmail) {
                        iconsListHtml += _.template(action_icon, {
                            iconCssClass: this.mailClient.aliasButtonNewEmail,
                            label: 'новое письмо'
                        });
                    }
                    if (addButtonReply) {
                        iconsListHtml += _.template(action_icon, {
                            iconCssClass: this.mailClient.aliasButtonReply,
                            label: 'ответить'
                        });
                    }
                    if (addButtonReplyAll) {
                        iconsListHtml += _.template(action_icon, {
                            iconCssClass: this.mailClient.aliasButtonReplyAll,
                            label: 'ответить всем'
                        });
                    }
                    if (addButtonForward) {
                        iconsListHtml += _.template(action_icon, {
                            iconCssClass: this.mailClient.aliasButtonForward,
                            label: 'переслать'
                        });
                    }
                    if (addButtonSaveDraft) {
                        iconsListHtml += _.template(action_icon, {
                            iconCssClass: this.mailClient.aliasButtonSaveDraft,
                            label: 'сохранить'
                        });
                    }
                    if (addButtonSend) {
                        iconsListHtml += _.template(action_icon, {
                            iconCssClass: this.mailClient.aliasButtonSend,
                            label: 'отправить'
                        });
                    }
                    if (addButtonSendDraft) {
                        iconsListHtml += _.template(action_icon, {
                            iconCssClass: this.mailClient.aliasButtonSendDraft,
                            label: 'отправить черновик'
                        });
                    }
                    if (addButtonMoveToTrash) {
                        iconsListHtml += _.template(action_icon, {
                            iconCssClass: this.mailClient.aliasButtonMoveToTrash,
                            label: 'удалить'
                        });
                    }
                    if (addButtonRestore) {
                        iconsListHtml += _.template(action_icon, {
                            iconCssClass: this.mailClient.aliasButtonRestore,
                            label: 'восстановить'
                        });
                    }
                    // conpose HTML code }

                    // render HTML
                    this.$('.actions').html(iconsListHtml);

                    // this.delegateEvents();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            doMoveToTrashActiveEmail: function () {
                try {
                    if (undefined === this.mailClient.activeEmail) {
                        return;
                    }

                    this.doMoveToTrash(this.mailClient.activeEmail);
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
            doMoveToTrash: function (email, cb) {
                try {
                    var me = this;

                    SKApp.server.api(
                        'mail/move',
                        {
                            folderId: this.mailClient.codeFolderTrash,
                            messageId: email.mySqlId
                        },
                        function () {
                            try {
                                var updateFolderRender = function () {
                                    try {
                                        me.mailClient.setActiveEmail(undefined);
                                        me.isSortingNotApplied = true;
                                        var inboxEmails = me.mailClient.getInboxFolder().emails;

                                        for (var i in inboxEmails) {
                                            me.mailClient.setActiveEmail(inboxEmails[i]);
                                            break;
                                        }

                                        // logging:
                                        me.mailClient.setWindowsLog(
                                            'mailMain',
                                            me.mailClient.getActiveEmailId()
                                        );

                                        me.updateFolderLabels();
                                        me.renderInboxFolder();

                                    } catch(exception) {
                                        if (window.Raven) {
                                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                        }
                                    }
                                };

                                me.mailClient.getTrashFolderEmails(
                                    me.mailClient.getInboxFolderEmails(
                                        updateFolderRender
                                    )
                                );
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        }
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            doMoveToInboxByClick: function () {
                try {
                    if (undefined === this.mailClient.activeEmail) {
                        return;
                    }

                    this.doMoveToInbox(this.mailClient.activeEmail);
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
            doMoveToInbox: function (email) {
                try {
                    var me = this;

                    SKApp.server.api(
                        'mail/move',
                        {
                            folderId: this.mailClient.codeFolderInbox,
                            messageId: email.mySqlId
                        },
                        function () {
                            try {
                                var updateFolderRender = function () {
                                    try {
                                        me.mailClient.setActiveEmail(undefined);
                                        var trashEmails = me.mailClient.getTrashFolder().emails;
                                        for (var i in trashEmails) {
                                            me.mailClient.setActiveEmail(trashEmails[i]);
                                            break;
                                        }

                                        // logging:
                                        me.mailClient.setWindowsLog(
                                            'mailMain',
                                            me.mailClient.getActiveEmailId()
                                        );

                                        me.updateFolderLabels();
                                        me.renderTrashFolder();
                                    } catch(exception) {
                                        if (window.Raven) {
                                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                        }
                                    }
                                };

                                me.mailClient.getInboxFolderEmails(
                                    me.mailClient.getTrashFolderEmails(
                                        updateFolderRender
                                    )
                                );
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
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
            hideFoldersBlock: function () {
                try {
                    $("#" + this.mailClientScreenID + " nav").hide();
                    $("#" + this.mailClientContentBlockId).css('margin-left', '-180px');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            unhideFoldersBlock: function () {
                try {
                    $("#" + this.mailClientContentBlockId).css('margin-left', '0px');
                    $("#" + this.mailClientScreenID + " nav").show();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            renderWriteCustomNewEmailScreen: function (event, icons, draftEmail, callback) {
                try {
                    this.mailClient.setActiveScreen(this.mailClient.screenWriteNewCustomEmail);
                    this.mailClient.newEmailUsedPhrases = [];
                    this.mailClient.availableSubjects = [];
                    this.mailClient.activeMailPrefix = null;

                    // родитель есть только у RE и FWD
                    this.mailClient.activeParentEmailMyQslId = null;

                    var mailClientView = this;

                    // get template
                    var htmlSceleton = _.template(mail_client_new_email_template, {});

                    this.hideFoldersBlock();

                    // render HTML sceleton
                    this.$("#" + this.mailClientContentBlockId).html(htmlSceleton);

                    if (undefined === icons) {
                        icons = mailClientView.mailClient.iconsForWriteEmailScreenArray;
                    }

                    this.renderIcons(icons);

                    if (undefined === draftEmail) {
                        this.updateSubjectsList();
                    } else {
                        this.mailClient.availableSubjects.push(draftEmail.subject);
                        mailClientView.updateSubjectsList(true);
                    }
                    // add attachments list {
                    this.mailClient.uploadAttachmentsList(function () {
                        var attachmentsListHtml = [];
                        attachmentsListHtml.push({
                            text: "без вложения.",
                            value: 0,
                            selected: 1,
                            imageSrc: ""
                        });

                        mailClientView.mailClient.availableAttachments.forEach(function (attachment) {
                            attachmentsListHtml.push({
                                text: attachment.label,
                                value: attachment.fileId,
                                imageSrc: attachment.getIconImagePath()
                            });
                        });

                        mailClientView.$("#MailClient_NewLetterAttachment div.list").ddslick('destroy');
                        mailClientView.$("#MailClient_NewLetterAttachment div.list").ddslick({
                            data: attachmentsListHtml,
                            width: '100%',
                            height: '245px',
                            selectText: "Нет вложения.",
                            imagePosition: "left"
                        });

                        if (undefined !== draftEmail && undefined !== draftEmail.attachment) {
                            var attachmentIndex = _.indexOf(
                                mailClientView.mailClient.availableAttachments.map(function (attachment) {
                                    return attachment.fileMySqlId;
                                }),
                                draftEmail.attachment.fileMySqlId
                            );

                            mailClientView.$("#MailClient_NewLetterAttachment div.list").ddslick(
                                "select", {index: attachmentIndex + 1 }
                            );
                        }

                        if(typeof callback === 'function') {
                            callback();
                        }
                    });

                    // add attachments list }

                    var assignedRecipient = [];

                    if (undefined !== draftEmail) {
                        _.each(SKApp.simulation.characters.models, function(character){
                            if (-1 < draftEmail.recipientNameString.indexOf(character.get('fio'))) {
                                assignedRecipient.push(character.getFormatedForMailToName());
                            }
                        });
                    }

                    this.$("#MailClient_RecipientsList").tagHandler({
                        className: 'tagHandler recipients-list-widget',
                        assignedTags:  assignedRecipient,
                        availableTags: SKApp.simulation.mailClient.getFormatedCharacterList(),
                        autocomplete: true,
                        allowAdd: false,
                        msgNoNewTag: "Вы не можете написать письмо данному получателю",
                        onAdd: function (tag) {
                            try {
                                var me = this;
                                var add = SKApp.simulation.mailClient.reloadSubjectsWithWarning(
                                    mailClientView.getCurrentEmailRecipientIds(),
                                    'add',
                                    undefined,
                                    function () {
                                        $("#MailClient_RecipientsList")[0].addTag(me, tag);
                                    }
                                );
                                return add;
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        },
                        afterDelete: function (tag) {
                            try {
                                var subject = mailClientView.$("#MailClient_NewLetterSubject input.dd-selected-value").val();
                                var curRec = mailClientView.currentRecipients;
                                var availablePhrases = SKApp.simulation.mailClient.availablePhrases;
                                if(curRec !== undefined && curRec.indexOf(tag) === 0 && curRec.length === 1 && subject === "") {
                                    SKApp.simulation.mailClient.reloadSubjects(mailClientView.getCurrentEmailRecipientIds());
                                    mailClientView.updateSubjectsList();
                                }else if(curRec !== undefined && curRec.indexOf(tag) === 0 && subject === ""){
                                    SKApp.simulation.mailClient.reloadSubjects(mailClientView.getCurrentEmailRecipientIds());
                                }else if(curRec !== undefined && curRec.indexOf(tag) === 0 && availablePhrases.length === 0){
                                    SKApp.simulation.mailClient.reloadSubjects(mailClientView.getCurrentEmailRecipientIds());
                                    mailClientView.updateSubjectsList();
                                }else if(mailClientView.$("#MailClient_RecipientsList li.tagItem").get().length === 0){
                                    mailClientView.clearSubject();
                                }
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        },
                        afterAdd: function(tag) {
                            if(mailClientView.$("#MailClient_RecipientsList li.tagItem").get().length === 1) {
                                mailClientView.$("#mailEmulatorNewLetterText").html('');
                                SKApp.simulation.mailClient.reloadSubjects(mailClientView.getCurrentEmailRecipientIds());
                            }
                        },
                        onDelete: function(tag) {
                            mailClientView.currentRecipients = $("#MailClient_RecipientsList li.tagItem").map(function() {
                                return $(this).text();
                            }).get();
                            var me = this;
                            var del = SKApp.simulation.mailClient.reloadSubjectsWithWarning(
                                mailClientView.getCurrentEmailRecipientIds(),
                                'delete',
                                undefined,
                                function() {
                                    try {
                                        $("#MailClient_RecipientsList")[0].removeTag(me);
                                    } catch(exception) {
                                        if (window.Raven) {
                                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                        }
                                    }
                                },
                                me,
                                function() {
                                    try {
                                        SKApp.simulation.mailClient.reloadSubjects(mailClientView.getCurrentEmailRecipientIds());
                                        mailClientView.updateSubjectsList();
                                    } catch(exception) {
                                        if (window.Raven) {
                                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                        }
                                    }
                                }
                            );
                            return del;
                        }
                    });

                    //this.$('#MailClient_RecipientsList input').focus();
                    //this.$('#MailClient_RecipientsList input').blur();

                    // add IDs to lists of recipients and copies - to simplify testing
                    this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(0)').find('a'));

                    var assignedCopy = [];

                    if (undefined !== draftEmail) {
                        _.each(SKApp.simulation.characters.models, function(character) {
                            try {
                                if (-1 < draftEmail.copyToString.indexOf(character.get('fio'))) {
                                    assignedCopy.push(character.getFormatedForMailToName());
                                }
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        });
                    }

                    // fills copyTo list
                    this.$("#MailClient_CopiesList").tagHandler({
                        className: 'tagHandler copy-list-widget',
                        assignedTags: assignedCopy,
                        availableTags: mailClientView.mailClient.getFormatedCharacterList(),
                        autocomplete: true,
                        allowAdd: false,
                        msgNoNewTag: "Вы не можете написать письмо данному получателю"
                    });

                    //this.$('#MailClient_CopiesList input').focus();
                    //this.$('#MailClient_CopiesList input').blur();

                    // add IDs to lists of recipients and copies - to simplify testing
                    this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(1)').find('a'));

                    // this.delegateEvents();
                    this.mailClient.setWindowsLog('mailNew');

                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param elements
             */
            updateIdsForCharacterlist: function (elements) {
                try {
                    var me = this;
                    // items appended to body, so this.$ not works
                    $(elements).each(function () {
                        try {
                            var character = me.mailClient.getRecipientByName($(this).text());
                            $(this).attr('data-character-id', character.get('code'));
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
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
             * @returns {Array}
             */
            getCurrentEmailRecipientIds: function () {
                try {
                    var list = [];
                    var valuesArray = this.$("#MailClient_RecipientsList li.tagItem").get();
                    var fio;
                        $.each(valuesArray, function(index, value) {
                            try {
                                fio = $(value).text().replace(/\s\((.*)\)/g, '');//remove title
                                var character = _.first(SKApp.simulation.characters.where({'fio': fio}));
                                list.push(character.get('id'));
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        });
                    return list;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Возвращает id первого адремата письма в форме отправки нового письма
             * @method
             * @returns number
             */
            getCurrentEmailRecipientId: function() {
                try {
                   var characters = this.getCurrentEmailRecipientIds();
                    return _.first(characters);
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
            getCurentEmailCopiesIds: function() {
                try {
                    var list = [];

                    var valuesArray = $("#MailClient_CopiesList").find("li").get();
                    SKApp.simulation.characters.each(function (character) {
                        try {
                            _.each(valuesArray, function (value) {
                                try {
                                    // get IDs of character by label text comparsion
                                    if ($(value).text() && $(value).text() === character.getFormatedForMailToName()) {
                                        list.push(character.get('id'));
                                    }
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            });
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });

                    return list;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            updateSubjectsList: function (forceAllowChangeSubject) {
                try {
                    var subjects_list = [];

                    for (var i in this.mailClient.availableSubjects) {
                        subjects_list.push({
                            text: this.mailClient.availableSubjects[i].text,
                            value: parseInt(this.mailClient.availableSubjects[i].themeId)
                        });
                    }
                    if(subjects_list.length === 0){
                        subjects_list.push({
                            text: "без темы.",
                            value: 0,
                            selected: true
                        });
                    }
                    this.$("#MailClient_NewLetterSubject").ddslick('destroy');

                    var me = this;

                    this.$("#MailClient_NewLetterSubject").ddslick({
                            data: subjects_list,
                            width: '100%',
                            selectText: "без темы.",
                            imagePosition: "left",
                            onSelected: function (dataSelected) {
                                me.doUpdateMailPhrasesList(dataSelected);
                            }
                    });

                    if(subjects_list.length === 1 && this.mailClient.activeScreen !== 'SCREEN_WRITE_NEW_EMAIL') {
                        this.$("#MailClient_NewLetterSubject").ddslick('select', {'index':0 });
                    }

                    if (undefined === forceAllowChangeSubject) {
                        forceAllowChangeSubject = false;
                    }

                    if(this.mailClient.activeScreen !== 'SCREEN_WRITE_NEW_EMAIL' && false === forceAllowChangeSubject) {
                        this.$("#MailClient_NewLetterSubject").ddslick('disable');
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param {SKMailSubject} subject
             */
            renderSingleSubject: function (subject) {
                try {
                    var subjects_list = [];
                    subjects_list.push({
                        text: subject.getText(),
                        value: subject.themeId,
                        selected: true
                    });

                    var me = this;
                    this.$("#MailClient_NewLetterSubject").ddslick({
                        data: subjects_list,
                        width: '100%',
                        imagePosition: "left",
                        onSelected: function (dataSelected) {
                            try {
                                me.doUpdateMailPhrasesList(dataSelected);
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        }
                    });
                    if(this.mailClient.activeScreen !== 'SCREEN_WRITE_NEW_EMAIL'){
                        this.$("#MailClient_NewLetterSubject").ddslick('disable');
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
            getCurrentEmailThemeId: function () {
                try {
                    var selectedData = this.$("#MailClient_NewLetterSubject").data('ddslick').selectedData;
                    return selectedData ? selectedData.value : undefined;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }

                    return undefined;
                }
            },

            /**
             * @method
             * @returns {*}
             */
            getCurentEmailSubjectText: function () {
                try {
                    return this.$("#MailClient_NewLetterSubject label.dd-selected-text").text();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            renderPhrases: function () {
                try {
                    var me = this,
                        mailClient = this.mailClient,
                        phrases = this.mailClient.availablePhrases,
                        addPhrases = this.mailClient.availableAdditionalPhrases;

                    var mainPhrasesHtml = "<table>";
                    var additionalPhrasesHtml = '';

                    var row;
                    var rows = [[],[],[],[],[],[],[]];

                    phrases.forEach(function (phrase) {
                        try {
                            row = _.template(mail_client_phrase_template, {
                                phraseUid: phrase.uid,
                                phraseId: phrase.mySqlId,
                                text: phrase.text,
                                className: 'column-' + phrase.columnNumber
                            });
                            if(rows[phrase.columnNumber-1] !== undefined) {
                                rows[phrase.columnNumber-1].push(row);
                            }
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });

                    var columns = "";
                    var column = "";
                    for(var i = 0; i < 8; i++){
                        column = '<tr class="row-' + i + '">';
                        for(var j = 0; j < 7; j++){
                            if(rows[j] === undefined || rows[j][i] === undefined){
                                column += '<td class="column-' + (j+1) + '"></td>';
                            }else{
                                column += rows[j][i];
                            }
                        }
                        column += '</tr>'
                        columns += column;
                    }

                    mainPhrasesHtml += columns+"</table>";

                    addPhrases.forEach(function (phrase) {
                        try {
                            additionalPhrasesHtml += _.template(phrase_item, {
                                phraseUid: phrase.uid,
                                phraseId: phrase.mySqlId,
                                text: phrase.text
                            });
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });
                    mainPhrasesHtml += "</table>";

                    if (phrases.length) {
                        this.$("#mailEmulatorNewLetterTextVariants").html(mainPhrasesHtml);
                        // this.$("#mailEmulatorNewLetterTextVariantsAdd").html(additionalPhrasesHtml);
                        this.$('#mailEmulatorNewLetterText').sortable();
                        this.$('.mail-tags-bl').show();
                        this.$('.mail-text-wrap').height(
                            this.$('.mail-view.new').height() - this.$('.mail-view-header').outerHeight() - this.$('.mail-tags-bl').outerHeight()
                        );
                    } else {
                        this.$('.mail-tags-bl').hide();
                        this.$('.mail-text-wrap').height(
                            this.$('.mail-view.new').height() - this.$('.mail-view-header').outerHeight()
                        );
                    }

                    // some letter has predefine text, update it
                    // if there is no text - this.mailClient.messageForNewEmail is empty string
                    mailClient.newEmailUsedPhrases = [];
                    if (mailClient.activeEmail && mailClient.activeEmail.phrases.length) {
                        mailClient.activeEmail.phrases.forEach(function(phraseId) {
                            try {
                                var phrase = mailClient.getAvailablePhraseByMySqlId(phraseId);
                                var phraseToAdd = new SKMailPhrase();

                                if (undefined !== phrase) {
                                    phraseToAdd.mySqlId = phrase.mySqlId;
                                    phraseToAdd.text = phrase.text;
                                    phraseToAdd.columnNumber = phrase.columnNumber;
                                    mailClient.newEmailUsedPhrases.push(phraseToAdd);
                                    me.renderAddPhraseToEmail(phraseToAdd);
                                }
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        });
                    }
                    this.setupDroppable();
                    this.renderTXT();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param event
             */
            doAddPhraseToEmail: function (event) {
                try {
                    var me = this;

                    event.preventDefault();
                    if(me.$('#MailClient_RecipientsList .tagItem').length === 0 || me.$('#MailClient_NewLetterSubject .dd-selected').text() === 'без темы.'){
                        me.message_window_phrase = new SKDialogView({
                            'message':'Для написания нового письма выберите тему и адресата.',
                            'buttons':[
                                {
                                    'value':'Ок',
                                    'onclick':function () {
                                        try {
                                            me.message_window_phrase.remove();
                                            delete me.message_window_phrase;
                                        } catch(exception) {
                                            if (window.Raven) {
                                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                            }
                                        }
                                    }
                                }
                            ]
                        });
                        return false;
                    }
                    if (this.blockPhraseMoving) {
                        return;
                    }

                    if (undefined === $(event.currentTarget).data('id') ||
                        null === $(event.currentTarget).data('id')) {
                        return;
                    }

                    this.blockPhraseMoving = true;

                    // может отказаться от использования event.currentTarget
                    // google, правда, пишет что это самый правильный вариант
                    var phrase = this.mailClient.getAvailablePhraseByMySqlId($(event.currentTarget).data('id'));

                    // event.currentTarget может вернуть <a> или <span> и тогда data('id') == undefined
                    // event.target гарантировано вернёт span
                    if (undefined === phrase) {
                        var phrase = this.mailClient.getAvailablePhraseByMySqlId(
                            $(event.target).parent().parent().data('id')
                        );
                    }

                    if (undefined === phrase) {
                        throw new Error(
                            'Undefined phrase id. '
                                + $(event.currentTarget).data('id')
                                + '; '
                                + $(event.target).parent().parent().data('id')
                                + '; '
                                + $(event.currentTarget).text()
                        );
                    }

                    // simplest way to clone small object in js {
                    var phraseToAdd = new SKMailPhrase(); // generate unique uid
                    phraseToAdd.mySqlId = phrase.mySqlId;
                    phraseToAdd.text = phrase.text;
                    phraseToAdd.columnNumber = phrase.columnNumber;
                    // simplest way to clone small object in js }

                    // ADD:

                    this.mailClient.newEmailUsedPhrases.push(phraseToAdd);

                    // render updated state
                    this.renderAddPhraseToEmail(phraseToAdd);

                    setTimeout(function() {
                        try {
                            delete me.blockPhraseMoving;
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    }, 400);
                } catch(exception) {
                    delete me.blockPhraseMoving;
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param phrase
             */
            renderAddPhraseToEmail: function (phrase) {
                try {
                    var phraseHtml = _.template(phrase_item, {
                        phraseUid: phrase.uid,
                        phraseId: phrase.mySqlId,
                        text: phrase.text
                    });

                    $("#mailEmulatorNewLetterText").append(phraseHtml);

                    // this.delegateEvents();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param event
             */
            doRemovePhraseFromEmail: function (event) {
                try {
                    var me = this;

                    event.preventDefault();

                    if (this.blockPhraseMoving) {
                        return;
                    }

                    this.blockPhraseMoving = true;
                    var phrase = this.mailClient.getUsedPhraseByUid($(event.currentTarget).data('uid'));

                    if (undefined === phrase) {
                        // if a have seweral (2,3,4...) phrases added to email - click handled twise
                        // currently I ignore this bug.
                        // @todo: fix it
                        throw new Error ('Undefined phrase uid.');
                    }

                    this.removePhraseFromEmail(phrase);

                    var phrases = this.mailClient.newEmailUsedPhrases;
                    for (var i in phrases) {
                        // keep '==' not strict!
                        if (phrases[i].uid == phrase.uid) {
                            phrases.splice(i, 1);
                        }
                    }

                    setTimeout(function() {
                        try {
                            delete me.blockPhraseMoving;
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    }, 400);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param phrase
             */
            removePhraseFromEmail: function (phrase) {
                try {
                    this.$("#mailEmulatorNewLetterText li[data-uid=" + phrase.uid + "]").remove();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @return SKAttachment | undefined
             */
            getCurrentEmailAttachment: function () {
                try {
                    var selectedAttachmentLabel = this.$('#MailClient_NewLetterAttachment .dd-selected label').text();
                    var attachments = this.mailClient.availableAttachments;

                    if (undefined !== selectedAttachmentLabel && null !== selectedAttachmentLabel) {
                        for (var i in attachments) {
                            if (selectedAttachmentLabel === attachments[i].label) {
                                return attachments[i];
                            }
                        }
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }

                return undefined;
            },

            setupDroppable:function () {
                try {
                    var me = this;
                    $("#mailEmulatorNewLetterTextVariants .phrase-item").draggable({
                        helper: 'clone',
                        drag:function (event, ui) {
                        },
                        start:function (event, ui) {
                        },
                        stop:function (event, ui) {
                        }

                    });

                    $("#mailEmulatorNewLetterText .phrase-item").draggable({
                        helper: 'clone',
                        drag:function (event, ui) {
                        },
                        start:function (event, ui) {
                        },
                        stop:function (event, ui) {
                        }

                    });
                    $(".mail-text-wrap").droppable({
                        drop:function (event, ui) {
                            try {
                                if(me.$('#MailClient_RecipientsList .tagItem').length === 0 || me.$('#MailClient_NewLetterSubject .dd-selected').text() === 'без темы.'){
                                    me.message_window_phrase = new SKDialogView({
                                        'message':'Для написания нового письма выберите тему и адресата.',
                                        'buttons':[
                                            {
                                                'value':'Ок',
                                                'onclick':function () {
                                                    try {
                                                        me.message_window_phrase.remove();
                                                        delete me.message_window_phrase;
                                                    } catch(exception) {
                                                        if (window.Raven) {
                                                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                                        }
                                                    }
                                                }
                                            }
                                        ]
                                    });
                                    return false;
                                }
                                var phrase_id = $(ui.helper).parent().data('id');
                                if(phrase_id !== undefined){
                                    var phrase = me.mailClient.getAvailablePhraseByMySqlId(phrase_id);
                                    var phraseToAdd = new SKMailPhrase(); // generate unique uid
                                    phraseToAdd.mySqlId = phrase.mySqlId;
                                    phraseToAdd.text = phrase.text;
                                    phraseToAdd.columnNumber = phrase.columnNumber;
                                    // simplest way to clone small object in js }

                                    // ADD:

                                    me.mailClient.newEmailUsedPhrases.push(phraseToAdd);

                                    // render updated state
                                    me.renderAddPhraseToEmail(phraseToAdd);
                                }
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        }
                    });
                    $(".mail-tags-bl").droppable({
                        drop:function (event, ui) {
                            var phrase_uid = $(ui.helper).data('uid');
                            if(phrase_uid !== undefined) {
                                var phrase = me.mailClient.getUsedPhraseByUid(phrase_uid);

                                me.removePhraseFromEmail(phrase);

                                var phrases = me.mailClient.newEmailUsedPhrases;
                                for (var i in phrases) {
                                    // keep '==' not strict!
                                    if (phrases[i].uid == phrase.uid) {
                                        phrases.splice(i, 1);
                                    }
                                }
                            }

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
             * @returns {Array}
             */
            getCurrentEmailPhraseIds: function () {
                try {
                    var list = [];

                    var usedPhrases = $("#mailEmulatorNewLetterText li").get();

                    for (var i in usedPhrases) {
                        list.push($(usedPhrases[i]).data('id'));
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
             * @returns {SKEmail}
             */
            generateNewEmailObject: function () {
                try {
                    var emailToSave = new SKEmail();
                    var me = this;

                    // recipients
                    var recipients = this.getCurrentEmailRecipientIds();
                    emailToSave.recipients = []; // set empty really necessary
                    for (var i in recipients) {
                        emailToSave.recipients.push(this.mailClient.getCharacterById(recipients[i]));
                    }

                    // copies
                    var copies = this.getCurentEmailCopiesIds();
                    emailToSave.copyTo = []; // set empty really necessary
                    for (var i in copies) {
                        emailToSave.copyTo.push(this.mailClient.getCharacterById(copies[i]));
                    }

                    // subject
                    var subject = new SKMailSubject();
                    subject.themeId = this.getCurrentEmailThemeId();
                    subject.text = this.getCurentEmailSubjectText();
                    emailToSave.subject = subject;

                    // attachment
                    emailToSave.attachment = this.getCurrentEmailAttachment();

                    // phrases
                    var phrases = this.getCurrentEmailPhraseIds();
                    emailToSave.phrases = [];
                    _.each(phrases, function(phrase) {
                        try {
                            emailToSave.phrases.push(me.mailClient.getAvailablePhraseByMySqlId(phrase));
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });

                    // update
                    emailToSave.updateStatusPropertiesAccordingObjects();

                    emailToSave.mySqlId = this.mailClient.draftToEditEmailId;

                    return emailToSave;
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            doSaveEmailToDrafts: function() {
                try {
                    var me = this;
                    var emailToSave = this.generateNewEmailObject();

                    this.mailClient.saveToDraftsEmail(emailToSave, function() {
                        try {
                            me.updateFolderLabels();
                            me.renderActiveFolder();

                            setTimeout(function() {
                                try {
                                    me.renderActiveFolder();
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            }, 1000);

                            me.mailClient.setWindowsLog(
                                'mailMain',
                                me.mailClient.getActiveEmailId()
                            );
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
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
             */
            doSendEmail: function () {
                try {
                    var emailToSave = this.generateNewEmailObject();
                    var mailClientView = this;

                    this.mailClient.sendNewCustomEmail(emailToSave);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param iconsList
             */
            renderWriteEmailScreen: function (iconsList, isFillAttachmentsList) {
                try {
                    var mailClientView = this;

                    if (false != isFillAttachmentsList) {
                        isFillAttachmentsList = true; 
                    }

                    // get template
                    var htmlSceleton = _.template(mail_client_new_email_template, {});

                    this.hideFoldersBlock();

                    // render HTML sceleton
                    this.$("#" + this.mailClientContentBlockId).html(htmlSceleton);

                    if (undefined === iconsList) {
                        iconsList = this.mailClient.iconsForWriteEmailScreenArray;
                    }

                    this.renderIcons(iconsList);

                    // add attachments list {
                    if (isFillAttachmentsList) {
                        this.mailClient.uploadAttachmentsList(function () {
                            try {
                                var attachmentsListHtml = [];
                                attachmentsListHtml.push({
                                    text: "без вложения.",
                                    value: 0,
                                    selected: 1,
                                    imageSrc: ""
                                });

                                mailClientView.mailClient.availableAttachments.forEach(function (attachment) {
                                    try {
                                        attachmentsListHtml.push({
                                            text: attachment.label,
                                            value: attachment.fileId,
                                            imageSrc: attachment.getIconImagePath()
                                        });
                                    } catch(exception) {
                                        if (window.Raven) {
                                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                        }
                                    }
                                });

                                mailClientView.$("#MailClient_NewLetterAttachment div.list").ddslick('destroy');
                                mailClientView.$("#MailClient_NewLetterAttachment div.list").ddslick({
                                    data: attachmentsListHtml,
                                    width: '100%',
                                    height: '245px',
                                    selectText: "Нет вложения.",
                                    imagePosition: "left"
                                });
                                // add attachments list }

                                mailClientView.trigger('attachment:load_completed');
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        });
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            doUpdateMailPhrasesList: function (dataSelected) {
                try {
                    var themeId = dataSelected.selectedData.value;
                    var mailClientView = this;
                    var mailClient = this.mailClient;

                    if ((0 !== mailClient.availablePhrases.length || 0 !== mailClient.availableAdditionalPhrases.length) && mailClient.isNotEmptySubject()) {
                        // warning
                        if (mailClient.activeScreen !== "SCREEN_WRITE_FORWARD") {
                            this.message_window = new SKDialogView({
                                'message': 'Если вы измените тему письма, то обновится список доступных фраз и очистится текст письма.',
                                'buttons': [
                                    {
                                        'value': 'Продолжить',
                                        'onclick': function () {
                                            try {
                                                if(mailClient.activeEmail !== undefined){
                                                    mailClient.activeEmail.phrases = [];
                                                }

                                                mailClient.getAvailablePhrases(mailClientView.getCurrentEmailRecipientId(), themeId, function () {
                                                    mailClientView.$('#mailEmulatorNewLetterText').html('');
                                                    mailClientView.$('#mailEmulatorNewLetterText li').remove();

                                                });
                                                delete mailClient.message_window;
                                            } catch(exception) {
                                                if (window.Raven) {
                                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                                }
                                            }
                                        }
                                    },
                                    {
                                        'value': 'Вернуться',
                                        'onclick': function() {
                                            try {
                                                mailClientView.selectSubjectByValue(mailClient.newEmailThemeId);
                                                delete mailClient.message_window;
                                            } catch(exception) {
                                                if (window.Raven) {
                                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                                }
                                            }
                                        }
                                    }
                                ]
                            });
                        }
                    } else {
                        // standard way
                        mailClient.newEmailThemeId = themeId;

                        // all "fantastic" emails has TXT constructor - but this extra request return default B1,
                        // that is produce phrases render - it is wrong
                        if (false === mailClientView.isFantasticSend) {
                            mailClient.getAvailablePhrases(mailClientView.getCurrentEmailRecipientId(), themeId, function () {
                                try {
                                    mailClientView.$('#mailEmulatorNewLetterText').html('');
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            });
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
             * @param value
             */
            selectSubjectByValue: function (value) {
                try {
                    var me = this;
                    var index = null;
                    this.$("#MailClient_NewLetterSubject li a input").each(function(i, el) {
                        try {
                            if($(el).val() == value){
                                index = i;
                            }
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });

                    if(index === null){
                        return;
                    }

                    var ddData = this.$("#MailClient_NewLetterSubject").data('ddslick').settings.data;
                    this.$("#MailClient_NewLetterSubject").ddslick('destroy');
                    this.$("#MailClient_NewLetterSubject").ddslick({
                        data: ddData,
                        width: '100%',
                        defaultSelectedIndex:index,
                        onSelected: function (dataSelected) {
                            try {
                                me.doUpdateMailPhrasesList(dataSelected);
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
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
             * @param text
             */
            renderPreviousMessage: function (text) {
                try {
                    if (undefined !== text && '' !== text && null !== text) {
                        text = '<pre><p>' + text + '</p></pre>';
                    }
                    this.$(".previouse-message-text").html(text);
                    // this.delegateEvents();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             */
            renderTXT: function () {
                try {
                    // hide phrases in fantastic way
                    if (undefined !== this.mailClient.messageForNewEmail && '' !== this.mailClient.messageForNewEmail) {
                        this.$('.mail-tags-bl').hide();
                        this.$('.mail-text-wrap').height(
                            this.$('.mail-view.new').height() - this.$('.mail-view-header').outerHeight()
                        );
                        this.$('#mailEmulatorNewLetterText').html(
                            this.mailClient.messageForNewEmail.replace('\n', "<br />", "g").replace('\n\r', "<br />", "g")
                        );
                        this.$('.mail-text-wrap').height(
                            this.$('.mail-view.new').height() - this.$('.mail-view-header').outerHeight()
                        );
                    } else {
                        this.$('.mail-tags-bl').show();
                        this.$('.mail-text-wrap').height(
                            this.$('.mail-view.new').height() - this.$('.mail-view-header').outerHeight() - this.$('.mail-tags-bl').outerHeight()
                        );
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param response
             * @returns {boolean}
             */
            fillMessageWindow: function (response, icons, isAllowEdit) {
                try {
                    var me = this;
                    // set defaults {
                    if (undefined === isAllowEdit) {
                        isAllowEdit = false;
                    }

                    if (response.id) {
                        me.mailClient.draftToEditEmailId = response.id;
                    }

                    if (null === response.themeId) {
                        this.doRenderFolder(this.mailClient.aliasFolderInbox, false);
                        this.renderNullSubjectIdWarning('Вы не можете ответить на это письмо.');
                        return  false;
                    }

                    if (undefined === icons) {
                        icons = this.mailClient.iconsForWriteEmailScreenArray;
                    }
                    // set defaults }

                    if (false === isAllowEdit) {
                        this.renderWriteEmailScreen(icons);

                        var subject = new SKMailSubject();
                        subject.text = response.theme;
                        subject.mySqlId = response.themeId;
                        subject.themeId = response.themeId;
                        this.parentSubject = subject;
                        this.renderSingleSubject(subject);

                        // even if there is one recipient,but it must be an array
                        var recipient = [SKApp.simulation.mailClient.getRecipientByMySqlId(response.receiver_id)
                            .getFormatedForMailToName()];
                        var recipients = recipient;

                        this.$("#MailClient_RecipientsList .tagInput").remove(); // because "allowEdit:false"

                        // set recipients
                        this.$("#MailClient_RecipientsList").tagHandler({
                            className:     'tagHandler recipients-list-widget',
                            assignedTags:  recipient,
                            availableTags: recipients,
                            allowAdd:      isAllowEdit,
                            allowEdit:     isAllowEdit
                        });

                        // if user can edit recipients - than push all recipients to drop-down list }

                        //this.$('#MailClient_RecipientsList').focus();
                        //this.$('#MailClient_RecipientsList').blur();

                        // add IDs to lists of recipients and copies - to simplify testing
                        this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(0)').find('a'));

                        // add copies if they exests {
                        var copies = [];
                        if (undefined !== response.copiesIds) {
                            var ids = response.copiesIds.split(',');
                            for (var i in ids) {
                                if (0 < parseInt(ids[i], 10)) {
                                    copies.push(SKApp.simulation.mailClient.getRecipientByMySqlId(parseInt(ids[i],10))
                                        .getFormatedForMailToName());
                                }
                            }
                        }

                        $("#MailClient_CopiesList").tagHandler({
                            className: 'tagHandler copy-list-widget',
                            assignedTags: copies,
                            availableTags: SKApp.simulation.mailClient.getFormatedCharacterList(),
                            autocomplete: true
                        });

                        //this.$('#MailClient_CopiesList').focus();
                        //this.$('#MailClient_CopiesList').blur();

                    }

                    this.mailClient.messageForNewEmail = response.phrases.message;
                    this.renderPreviousMessage(response.phrases.previouseMessage);

                    this.renderTXT();

                    // add IDs to lists of recipients and copies - to simplify testing
                    this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(1)').find('a'));

                    // prevent custom text input
                    this.$("#MailClient_RecipientsList input").attr('readonly', 'readonly');
                    this.$("#MailClient_CopiesList input").attr('readonly', 'readonly');
                    // add copies if they exests }

                    // set attachment
                    if (response.attachmentId) {
                        this.once('attachment:load_completed', function () {
                            var attachmentIndex = 0;
                            $.each(me.mailClient.availableAttachments, function(index, attachment) {
                                try {
                                    if(response.attachmentId === attachment.fileMySqlId){
                                        attachmentIndex = index;
                                        return false;
                                    }
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            });
                            me.$("#MailClient_NewLetterAttachment div.list").ddslick("select", {index: attachmentIndex + 1 });
                        });

                    }

                    // add phrases {
                    if (null === response.phrases.message || '' === response.phrases.message
                        || undefined === response.phrases.message || 'undefined' == typeof response.phrases.message) {
                        SKApp.simulation.mailClient
                            .setRegularAvailablePhrases(response.phrases.data);

                        SKApp.simulation.mailClient
                            .setAdditionalAvailablePhrases(response.phrases.addData);
                        this.renderPhrases();
                    }

                    // add phrases }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            messageBodyView:function() {
                try {
                    this.$('.mail-text-wrap').height(
                        this.$('.mail-view.new').height() - this.$('.mail-view-header').outerHeight() - this.$('.mail-tags-bl').outerHeight()
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param message
             */
            renderNullSubjectIdWarning: function (message) {
                try {
                    var mailClientView = this;

                    mailClientView.message_window = new SKDialogView({
                        'message': message,
                        'buttons': [
                            {
                                'value': 'Ок',
                                'onclick': function () {
                                    delete mailClientView.message_window;
                                }
                            }
                        ]
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method doUpdateScreenFromForwardEmailData
             * @param {Object} response API response
             * @param {Object} draftEmail email if edit
             */
            doUpdateScreenFromForwardEmailData: function (response, draftEmail) {
                try {
                    if (1 === parseInt(response.result, 10)) {

                        if (null === response.subjectId) {
                            this.doRenderFolder(this.mailClient.aliasFolderInbox, false);
                            this.renderNullSubjectIdWarning('Вы не можете переслать это письмо.');
                            return  false;
                        }

                        this.mailClient.draftToEditEmailId = response.id;

                        if (draftEmail) {
                            this.renderWriteEmailScreen(this.mailClient.iconsForEditDraftDraftScreenArray);
                        } else {
                            if (response.attachmentId) {
                                this.renderWriteEmailScreen(this.mailClient.iconsForWriteEmailScreenArray, false);
                            } else {
                                this.renderWriteEmailScreen(this.mailClient.iconsForWriteEmailScreenArray);
                            }
                        }

                        var subject = new SKMailSubject();
                        subject.text = response.theme;
                        subject.mySqlId = response.themeId;
                        subject.parentMySqlId = response.parentThemeId;
                        subject.themeId = response.themeId;

                        this.renderSingleSubject(subject);

                        this.renderPreviousMessage(response.phrases.previouseMessage);
                        var me = this;

                        var assignedRecipient = [];

                        if (undefined !== draftEmail) {
                            _.each(SKApp.simulation.characters.models, function(character) {
                                try {
                                    if (-1 < draftEmail.recipientNameString.indexOf(character.get('fio'))) {
                                        assignedRecipient.push(character.getFormatedForMailToName());
                                    }
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            });
                        }

                        // set recipients
                        $("#MailClient_RecipientsList").tagHandler({
                            className: 'tagHandler recipients-list-widget',
                            assignedTags:  assignedRecipient,
                            availableTags: SKApp.simulation.mailClient.getFormatedCharacterList(),
                            autocomplete: true,
                            onAdd: function (tag) {
                                try {
                                    var add = SKApp.simulation.mailClient.reloadSubjectsWithWarning(
                                        me.getCurrentEmailRecipientIds(),
                                        'add_fwd',
                                        subject,
                                        function () {
                                            $("#MailClient_RecipientsList")[0].addTag(tag);
                                        }
                                    );
                                    return add;
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            },
                            afterDelete: function (tag) {
                                //
                            },
                            afterAdd: function (tag) {
                                try {
                                    SKApp.simulation.mailClient.reloadSubjects(me.getCurrentEmailRecipientIds(), subject, function(){
                                        SKApp.simulation.mailClient.getAvailablePhrases(me.getCurrentEmailRecipientId(), SKApp.simulation.mailClient.availableSubjects[0].themeId, function(){ });
                                    });
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            },
                            onDelete: function (tag) {
                                try {
                                    var el = this;
                                    var del = SKApp.simulation.mailClient.reloadSubjectsWithWarning(
                                        me.getCurrentEmailRecipientIds(),
                                        'delete_fwd',
                                        undefined,
                                        function () {
                                            $("#MailClient_RecipientsList")[0].removeTag(el);
                                        },
                                        el
                                    );
                                    return del;
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            }
                        });

                        //this.$('#MailClient_RecipientsList').focus();
                        //this.$('#MailClient_RecipientsList').blur();

                        // add IDs to lists of recipients and copies - to simplify testing
                        this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(0)').find('a'));

                        var assignedCopy = [];

                        if (undefined !== draftEmail) {
                            _.each(SKApp.simulation.characters.models, function(character) {
                                try  {
                                    if (-1 < draftEmail.copyToString.indexOf(character.get('fio'))) {
                                        assignedCopy.push(character.getFormatedForMailToName());
                                    }
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            });
                        }

                        $("#MailClient_CopiesList").tagHandler({
                            className: 'tagHandler copy-list-widget',
                            assignedTags:  assignedCopy,
                            availableTags: SKApp.simulation.mailClient.getFormatedCharacterList(),
                            autocomplete: true
                        });

                        // set attachment
                        if (response.attachmentId) {
                            this.mailClient.uploadAttachmentsList(function () {
                                try {
                                    var attachmentsListHtml = [];

                                    var attach = new SKAttachment();
                                    attach.fileMySqlId = response.attachmentId;
                                    attach.label = response.attachmentName;
                                    attachmentsListHtml.push({
                                        text: attach.label,
                                        value: attach.fileMySqlId,
                                        selected: 1,
                                        imageSrc: attach.getIconImagePath()
                                    });

                                    me.mailClient.availableAttachments.forEach(function (attachment) {
                                         attachmentsListHtml.push({
                                             text: attachment.label,
                                             value: attachment.fileMySqlId,
                                             imageSrc: attachment.getIconImagePath()
                                         });
                                    });
                                    me.mailClient.availableAttachments.push(attach);
                                    me.$("#MailClient_NewLetterAttachment div.list").ddslick('destroy');
                                    me.$("#MailClient_NewLetterAttachment div.list").ddslick({
                                        data: attachmentsListHtml,
                                        width: '100%',
                                        height: '255px',
                                        imagePosition: "left"
                                    });
                                    // add attachments list }

                                    me.trigger('attachment:load_completed');
                                } catch(exception) {
                                    if (window.Raven) {
                                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                    }
                                }
                            });
                        }

                        // add IDs to lists of recipients and copies - to simplify testing
                        this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(1)').find('a'));

                        // add phrases {
                        SKApp.simulation.mailClient
                            .setRegularAvailablePhrases(response.phrases.data);

                        SKApp.simulation.mailClient
                            .setAdditionalAvailablePhrases(response.phrases.addData);

                        this.renderPhrases();
                        // add phrases }
                    } else {
                        throw new Error ("Can`t initialize response email. View. #2");
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method renderReplyScreen
             */
            renderReplyScreen: function () {
                try {
                    if (undefined === this.mailClient.activeEmail) {
                        return;
                    }

                    this.mailClient.newEmailUsedPhrases = [];
                    var me = this;
                    this.mailClient.getDataForReplyToActiveEmail(function (response) {
                        try {
                            // strange, sometimes responce return to lile JSON but like some response object
                            // so we get JSON from it {
                            if (undefined === response.result && undefined !== response.responseText) {
                                response = $.parseJSON(response.responseText);
                            }
                            me.mailClient.activeConstructorCode    = response.phrases.constructorCode;
                            me.mailClient.activeMailPrefix         = response.mailPrefix;
                            me.mailClient.activeParentEmailMyQslId = response.messageId;
                            // so we get JSON from it }

                            if (false !== me.fillMessageWindow(response)) {
                                me.mailClient.setActiveScreen(me.mailClient.screenWriteReply);
                                me.mailClient.setWindowsLog('mailNew');
                            }
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method renderReplyAllScreen
             */
            renderReplyAllScreen: function () {
                try {
                    if (undefined === this.mailClient.activeEmail) {
                        return;
                    }

                    var me = this;
                    this.mailClient.newEmailUsedPhrases = [];

                    this.mailClient.getDataForReplyAllToActiveEmail(function (response) {
                        try {
                            if (false !== me.fillMessageWindow(response)) {
                                me.mailClient.activeConstructorCode    = response.phrases.constructorCode;
                                me.mailClient.activeMailPrefix         = response.mailPrefix;
                                me.mailClient.activeParentEmailMyQslId = response.messageId;
                                me.mailClient.setActiveScreen(me.mailClient.screenWriteReplyAll);
                                me.mailClient.setWindowsLog('mailNew');
                            }
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method renderForwardEmailScreen
             */
            renderForwardEmailScreen: function () {
                try {
                    if (undefined === this.mailClient.activeEmail) {
                        return;
                    }

                    var me = this;
                    this.mailClient.newEmailUsedPhrases = [];

                    this.mailClient.getDataForForwardActiveEmail(function (response) {
                        try {
                            // so we get JSON from it }
                            if (false !== me.doUpdateScreenFromForwardEmailData(response)) {
                                me.mailClient.activeMailPrefix         = response.mailPrefix;
                                me.mailClient.activeParentEmailMyQslId = response.messageId;
                                me.mailClient.setActiveScreen(me.mailClient.screenWriteForward);
                                me.mailClient.setWindowsLog('mailNew');
                            }
                        } catch(exception) {
                            if (window.Raven) {
                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                            }
                        }
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method doSendDraft
             */
            doSendDraft: function () {
                try {
                    if (undefined === this.mailClient.activeEmail) {
                        return;
                    }

                    var me = this;
                    me.mailClient.trigger('process:start');

                    SKApp.server.api(
                        'mail/sendDraft',
                        {
                            id: this.mailClient.activeEmail.mySqlId
                        },
                        function (response) {
                            try {
                                if (null == response) {
                                    me.mailClient.trigger('mail:sent');
                                    me.mailClient.trigger('process:finish');
                                    me.mailClient.setWindowsLog(
                                        'mailMain',
                                        me.mailClient.getActiveEmailId()
                                    );
                                } else if (1 !== response.result) {
                                    me.mailClient.trigger('process:finish');
                                    // display message for user
                                    SKApp.simulation.mailClient.message_window =
                                        SKApp.simulation.mailClient.message_window || new SKDialogView({
                                            'message': 'Не удалось отправить черновик адресату.',
                                            'buttons': [
                                                {
                                                    'value': 'Ок',
                                                    'onclick': function () {
                                                        try {
                                                            delete SKApp.simulation.mailClient.message_window;
                                                        } catch(exception) {
                                                            if (window.Raven) {
                                                                window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                                            }
                                                        }
                                                    }
                                                }
                                            ]
                                        });
                                } else {
                                    me.mailClient.trigger('mail:sent');
                                    me.mailClient.trigger('process:finish');
                                    me.mailClient.setWindowsLog(
                                        'mailMain',
                                        me.mailClient.getActiveEmailId()
                                    );
                                }

                                me.mailClient.draftToEditEmailId = undefined;

                                me.mailClient.getDraftsFolderEmails(function () {
                                    me.mailClient.getSendedFolderEmails();
                                    // get first email if email exist in folder {
                                    var draftEmails = me.mailClient.getDraftsFolder().emails;

                                    SKApp.simulation.mailClient.activeEmail = undefined;
                                    _.each(draftEmails, function(email){
                                        SKApp.simulation.mailClient.activeEmail = email;
                                    });
                                    // get first email if email exist in folder }

                                    me.renderDraftsFolder();
                                });
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
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
             * @method onMailSent
             */
            onMailOutboxUpdated: function () {
                try {
                    this.updateFolderLabels();
                    this.renderActiveFolder();

                    this.mailClient.setWindowsLog(
                        'mailMain',
                        this.mailClient.getActiveEmailId()
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            onMailSent: function() {
                try {
                    AppView.frame.icon_view.doSoundMailSent();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method onMailFantasticSend
             */
            onMailFantasticSend: function (email) {
                try {
                    var me = this;
                    me.isFantasticSend = true;
                        me.renderWriteCustomNewEmailScreen(undefined, undefined, undefined, function() {
                            me.mailClient.activeMailPrefix = email.mailPrefix;
                            me.fillMessageWindow(email);
                            var cursor = me.make('div', {'class': 'cursor'});
                            me.$el.append(cursor);
                            $(cursor)
                                .css('top', '500px')
                                .css('left', '500px')
                                .animate({
                                    'left': me.$('.SEND_EMAIL').offset().left + me.$('.SEND_EMAIL').width()/2,
                                    'top': me.$('.SEND_EMAIL').offset().top + me.$('.SEND_EMAIL').height()/2
                                }, 3400, function (){

                                    me.doSendEmail();

                                    setTimeout(function () {
                                        me.options.model_instance.close();
                                        me.isFantasticSend = false;
                                        me.mailClient.trigger('mail:fantastic-send:complete');
                                    }, 1000);
                                });
                        });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method onMailFantasticOpen
             */
            onMailFantasticOpen: function () {
                try {
                    var me = this;

                    if (this.$('.save-attachment-icon')) {
                        var docId = this.$('.save-attachment-icon').click().attr('data-document-id');
                        this.mailClient.once('attachment:saved', function () {
                            try {
                                $('.mail-popup-button').click();
                                var document = SKApp.simulation.documents.where({id: docId})[0];
                                var window = new SKDocumentsWindow({
                                    subname: 'documentsFiles',
                                    document: document,
                                    fileId: docId
                                });
                                window.open();
                                me.mailClient.trigger('mail:fantastic-open:complete');
                            } catch(exception) {
                                if (window.Raven) {
                                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                                }
                            }
                        });
                    } else {
                        // did not tested it
                        this.$('.mail-emulator-received-list-string-selected').click();
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method clearSubject
             */
            clearSubject: function() {
                try {
                    var subjects_list = [{
                            text: "без темы.",
                            value: 0,
                            selected: true
                        }];
                    this.$("#MailClient_NewLetterSubject").ddslick('destroy');

                    var me = this;
                    this.$("#MailClient_NewLetterSubject").ddslick({
                        data: subjects_list,
                        width: '100%',
                        selectText: "без темы.",
                        imagePosition: "left",
                        onSelected: function () {}
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method onMailProcessStart
             */
            onMailProcessStart: function() {
                try {
                    this.block();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method onMailProcessEnd
             */
            onMailProcessEnd: function() {
                try {
                    this.unBlock();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Стандартный родительский метод
             */
            onResize: function() {
                try {
                    window.SKWindowView.prototype.onResize.apply(this);
                    this.$('.mail-text-wrap').height(
                        this.$('.mail-view.new').height() - this.$('.mail-view-header').outerHeight() - this.$('.mail-tags-bl').outerHeight()
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @param SKEmail email
             */
            setActiveEmail: function (email) {
                try {
                    var email_data = this.$('#MailClient_IncomeFolder_List tr[data-email-id="'+email.mySqlId+'"]');

                    // защита от несуществующего email
                    if (null == $(email_data).data()) {
                        return;
                    }

                    this.doGetEmailDetails(
                        $(email_data).data().emailId,
                        this.mailClient.getActiveFolder().alias
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Стандартный родительский метод
             */
            onWindowClose: function() {
                try {
                    this.mailClient.activeEmail = undefined;

                    if (window.Raven) {
                        window.Raven.captureMessage(
                            'Log. Close mail client window.' + SKApp.simulation.getGameTime({with_seconds: true})
                        );
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });

    return SKMailClientView;
});
