/*global Backbone, _, SKApp, SKAttachment, SKMailSubject */
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
            dimensions: {
                maxWidth: 1100,
                maxHeight: 700
            },
            mailClient: undefined,

            addClass: 'mail-window',

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
                '#MailClient_ContentBlock .mail-tags-bl li': 'doAddPhraseToEmail',
                'click #mailEmulatorNewLetterText li': 'doRemovePhraseFromEmail',
                'click #MailClient_ContentBlock .mail-tags-bl li': 'doAddPhraseToEmail',
                'click .switch-size': 'doSwitchNewLetterView'
            }, SKWindowView.prototype.events),

            /**
             * Constructor
             * @method initialize
             */
            initialize: function () {
                var me = this;
                this.mailClient = SKApp.simulation.mailClient;
                this.mailClient.view = this;

                // init View according model
                this.listenTo(this.mailClient, 'init_completed', function () {
                    me.doRenderFolder(me.mailClient.aliasFolderInbox, true, true);
                    me.trigger('render_finished');
                });

                // render character subjects list
                this.listenTo(this.mailClient, 'mail:subject_list_in_model_updated', function () {

                    me.updateSubjectsList();

                    me.mailClient.availablePhrases = [];
                    me.mailClient.availableAdditionalPhrases = [];
                    me.renderPhrases();
                });

                // render phrases
                this.listenTo(this.mailClient, 'mail:available_phrases_reloaded', function () {
                    me.renderPhrases();
                });

                // update inbox emails counter
                this.listenTo(this.mailClient, 'mail:update_inbox_counter', function () {
                    var unreaded = me.mailClient.getInboxFolder().countUnreaded();
                    me.updateMailIconCounter(unreaded);
                    me.updateInboxFolderCounter(unreaded);
                });

                this.listenTo(this.mailClient, 'mail:sent', this.onMailSent);

                // close with conditions action {
                this.options.model_instance.on('pre_close', function () {
                    me.options.model_instance.prevent_close = !me.isCanBeClosed();

                    if (false == me.isCanBeClosed()) {
                        var mailClient = me.mailClient;
                        var mailClientView = me;

                        mailClientView.options.model_instance.prevent_close = true;

                        mailClient.message_window = new SKDialogView({
                            'message': 'Сохранить письмо в черновиках?',
                            'buttons': [
                                {
                                    'value': 'Не сохранять',
                                    'onclick': function () {
                                        mailClientView.renderActiveFolder();
                                    }
                                },
                                {
                                    'value': 'Отмена',
                                    'onclick': function () {
                                        delete mailClient.message_window;
                                    }
                                },
                                {
                                    'value': 'Сохранить',
                                    'onclick': function () {
                                        mailClientView.doSaveEmailToDrafts();
                                    }
                                }
                            ]
                        });
                    }
                });
                // close with conditions action }

                // call parrent initialize();
                SKWindowView.prototype.initialize.call(this);
            },

            /**
             * @method
             * @param event
             */
            doSwitchNewLetterView: function (event) {
                if ($(event.currentTarget).hasClass('min')) {
                    // maximize {
                    $(event.currentTarget).removeClass('min');
                    $(event.currentTarget).addClass('max');

                    $('.mail-view-header').addClass('min');
                    $('.mail-new-text').addClass('max');
                    $('.mail-new-text-scroll').addClass('max');
                    // maximize }
                } else {
                    // minimize {
                    $(event.currentTarget).removeClass('max');
                    $(event.currentTarget).addClass('min');

                    $('.mail-view-header').removeClass('min');
                    $('.mail-new-text').removeClass('max');
                    $('.mail-new-text-scroll').removeClass('max');
                    // minimize }
                }
            },

            /**
             * @method
             * @param event
             */
            doSaveAttachment: function (event) {
                this.mailClient.saveAttachmentToMyDocuments($(event.currentTarget).data('document-id'));
            },

            /**
             * @method
             */
            doAddToPlan: function () {
                var dialog = new SKMailAddToPlanDialog();
                dialog.render();
            },

            /**
             * @method
             * @param counter
             */
            updateMailIconCounter: function (counter) {
                var counterElement = $('#icons_email span');

                if (0 === counterElement.length) {
                    counterElement.html('<span></span>');
                    counterElement = $('#icons_email span');
                }

                counterElement.text(counter);
                if (0 === counter) {
                    counterElement.remove();
                }
            },

            /**
             * @method
             * @param counter
             */
            updateInboxFolderCounter: function (counter) {
                var el = $('.icon_' + this.mailClient.aliasFolderInbox + ' .counter');
                if (counter !== 0) {
                    el.text('(' + counter + ')');
                } else {
                    el.text('');
                }

            },

            /**
             * @method
             * @returns {boolean}
             */
            isCanBeClosed: function () {
                return (this.mailClient.activeScreen !== this.mailClient.screenWriteNewCustomEmail &&
                    this.mailClient.activeScreen !== this.mailClient.screenWriteReply &&
                    this.mailClient.activeScreen !== this.mailClient.screenWriteReplyAll &&
                    this.mailClient.activeScreen !== this.mailClient.screenWriteForward);
            },

            /**
             * @method
             */
            remove: function () {
                SKWindowView.prototype.remove.call(this);
                this.mailClient.setActiveScreen(undefined);
                this.mailClient.view = null;
            },

            /**
             * shows title block
             *
             * @method
             * @param el
             */
            renderTitle: function (el) {
                el.html(_.template(mail_client_title_template, {}));
                this.delegateEvents();
            },

            /**
             * Display (create if not exist) MailClient screen base
             *
             * @method
             */
            renderContent: function (el) {
                var mailClientWindowBasicHtml = _.template(mail_client_content_template, {
                    id: this.mailClientScreenID,
                    contentBlockId: this.mailClientContentBlockId
                });

                // append to <body>
                el.html(mailClientWindowBasicHtml);
                this.mailClient.getDataForInitialScreen();
            },

            /**
             * Used to get data for email preview
             * @todo move to model?
             *
             * @method
             */
            doGetEmailDetails: function (emailId, folderAlias) {
                var me = this;
                // do we have full data for current email ? {
                var email = SKApp.simulation.mailClient.folders[folderAlias].getEmailByMySqlId(emailId);

                if ('undefined' === typeof email) {
                    throw 'Try to render unexistent email ' + emailId + '.';
                }

                this.mailClient.setActiveEmail(email);

                // update active email in amails list {
                if (folderAlias === this.mailClient.aliasFolderInbox) {
                    this.updateInboxListView();
                }
                if (folderAlias === this.mailClient.aliasFolderSended) {
                    this.updateSendListView();
                }
                if (folderAlias === this.mailClient.aliasFolderDrafts) {
                    this.updateDraftsListView();
                }
                if (folderAlias === this.mailClient.aliasFolderTrash) {
                    this.updateTrashListView();
                }
                // update active email in amails list }

                if ('undefined' !== typeof email.text &&
                    'undefined' !== typeof email.attachment) {
                    // if YES - just render it
                    this.renderEmailPreviewScreen(
                        email,
                        this.mailClientInboxFolderEmailPreviewId,
                        '100%'
                    );

                    return;
                }
                // do we have full data for current email ? }

                // render preview
                me.renderEmailPreviewScreen(
                    email,
                    me.mailClientInboxFolderEmailPreviewId,
                    '100%'
                );
            },

            /**
             * @method
             */
            updateFolderLabels: function () {
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


                this.delegateEvents();
            },

            /**
             * @method
             * @param event
             */
            doRenderFolderByEvent: function (event) {
                // to named WTF is $(e.currentTarget).data('alias')
                var folderAlias = $(event.currentTarget).data('alias');

                this.doRenderFolder(folderAlias);
            },

            /**
             * @method
             * @param folderAlias
             * @param isSwitchToFirst
             * @param isInitialRender
             */
            doRenderFolder: function (folderAlias, isSwitchToFirst, isInitialRender) {
                var mailClientView = this;

                if (undefined === isSwitchToFirst) {
                    isSwitchToFirst = true;
                }

                if (undefined === isInitialRender) {
                    isInitialRender = false;
                }

                // script will assign table sotder for new folder
                this.isSortingNotApplied = true;

                this.mailClient.setActiveFolder(folderAlias);

                // clean up phrases when render folders
                this.mailClient.newEmailUsedPhrases = [];

                if (this.mailClient.aliasFolderInbox === folderAlias) {
                    if (isSwitchToFirst) {
                        this.mailClient.setActiveEmail(this.mailClient.getInboxFolder().getFirstEmail());
                    }
                    this.renderInboxFolder();
                }

                if (this.mailClient.aliasFolderSended === folderAlias) {
                    if (isSwitchToFirst) {
                        this.mailClient.setActiveEmail(this.mailClient.getSendedFolder().getFirstEmail());
                    }
                    this.renderSendFolder();
                }

                if (this.mailClient.aliasFolderDrafts === folderAlias) {
                    if (isSwitchToFirst) {
                        this.mailClient.setActiveEmail(this.mailClient.getDraftsFolder().getFirstEmail());
                    }
                    this.renderDraftsFolder();
                }

                if (this.mailClient.aliasFolderTrash === folderAlias) {
                    if (isSwitchToFirst) {
                        this.mailClient.setActiveEmail(this.mailClient.getTrashFolder().getFirstEmail());
                    }
                    this.renderTrashFolder();
                }

                this.updateFolderLabels();

                mailClientView.mailClient.setWindowsLog(
                    'mailMain',
                    mailClientView.mailClient.getActiveEmailId()
                );
            },

            /**
             * Renders Inbox folder content
             *
             * @method
             */
            updateInboxListView: function () {
                // generate emails list {
                var me = this;
                // We  use this 2 variables to separate emails to display unreaded emails first in list
                var emailsList = '';
                var incomingEmails = this.mailClient.folders[this.mailClient.aliasFolderInbox].emails; // to make code shorter

                _.values(incomingEmails).forEach(function (incomingEmail) {
                    // check is email active
                    var isActiveCssClass = '';
                    if (parseInt(incomingEmail.mySqlId, 10) === parseInt(me.mailClient.activeEmail.mySqlId, 10)) {
                        // why 2 CSS classes? - this is works
                        isActiveCssClass = ' mail-emulator-received-list-string-selected active ';
                    }

                    // generate HTML by template
                    emailsList += _.template(mail_client_income_line_template, {

                        emailMySqlId: incomingEmail.mySqlId,
                        senderName: incomingEmail.senderNameString,
                        subject: incomingEmail.subject.text,
                        sendedAt: incomingEmail.sendedAt,
                        isHasAttachment: incomingEmail.getIsHasAttachment(),
                        isHasAttachmentCss: incomingEmail.getIsHasAttachmentCss(),
                        isReadedCssClass: incomingEmail.getIsReadedCssClass(),
                        isActiveCssClass: isActiveCssClass
                    });

                });

                // add emails list
                this.$('#' + this.mailClientIncomeFolderListId + ' table tbody').html(emailsList);

                this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderInbox);
            },

            /**
             * @method
             */
            updateTrashListView: function () {
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
                    emailsList += _.template(trash_email_line, {

                        emailMySqlId: trashEmails[key].mySqlId,
                        senderName: trashEmails[key].senderNameString,
                        subject: trashEmails[key].subject.text,
                        sendedAt: trashEmails[key].sendedAt,
                        isHasAttachment: trashEmails[key].getIsHasAttachment(),
                        isHasAttachmentCss: trashEmails[key].getIsHasAttachmentCss(),
                        isReadedCssClass: true,
                        isActiveCssClass: isActiveCssClass
                    });
                }

                // add emails list
                $('#' + this.mailClientIncomeFolderListId + ' table tbody').html(emailsList);

                this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderTrash);
            },

            /**
             * @method
             */
            updateSendListView: function () {
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
                    emailsList += _.template(send_mail_line, {

                        emailMySqlId: sendedEmails[key].mySqlId,
                        recipientName: sendedEmails[key].getFormatedRecipientsString(),
                        subject: sendedEmails[key].subject.text,
                        sendedAt: sendedEmails[key].sendedAt,
                        isHasAttachment: sendedEmails[key].getIsHasAttachment(),
                        isHasAttachmentCss: sendedEmails[key].getIsHasAttachmentCss(),
                        isReadedCssClass: true,
                        isActiveCssClass: isActiveCssClass
                    });
                }

                // add emails list
                $('#' + this.mailClientIncomeFolderListId + ' table tbody').html(emailsList);

                this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderSended);
            },

            /**
             * @method
             */
            updateDraftsListView: function () {
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
                    emailsList += _.template(send_mail_line, {

                        emailMySqlId: draftEmails[key].mySqlId,
                        recipientName: draftEmails[key].getFormatedRecipientsString(),
                        subject: draftEmails[key].subject.text,
                        sendedAt: draftEmails[key].sendedAt,
                        isHasAttachment: draftEmails[key].getIsHasAttachment(),
                        isHasAttachmentCss: draftEmails[key].getIsHasAttachmentCss(),
                        isReadedCssClass: true,
                        isActiveCssClass: isActiveCssClass
                    });
                }

                // add emails list
                $('#' + this.mailClientIncomeFolderListId + ' table tbody').html(emailsList);

                this.addClickAndDoubleClickBehaviour(this.mailClient.aliasFolderDrafts);
            },

            /**
             * @method
             * @param folderAlias
             */
            addClickAndDoubleClickBehaviour: function (folderAlias) {
                var mailClientView = this,
                    folderId = this.mailClientIncomeFolderListId,
                    $table = mailClientView.$('#' + folderId + ' table');

                // Todo — move to events dictionary (GuGu)
                $('.email-list-line').click(function (event) {
                    // update lod data {

                    // if user click on same email line twice - open read email screen
                    // Do not change == to ===
                    if ($(event.currentTarget).data().emailId == mailClientView.mailClient.activeEmail.mySqlId) {
                        // log {
                        mailClientView.mailClient.setWindowsLog(
                            'mailPreview',
                            $(event.currentTarget).data().emailId
                        );
                        // log }
                        mailClientView.renderReadEmail(
                            mailClientView.mailClient.getEmailByMySqlId($(event.currentTarget).data().emailId)
                        );

                        mailClientView.mailClient.setActiveScreen(mailClientView.mailClient.screenReadEmail);
                    } else {
                        // if user clicks on different email lines - activate clicked line email
                        // log {
                        mailClientView.mailClient.setWindowsLog(
                            'mailMain',
                            $(event.currentTarget).data().emailId
                        );
                        // log }
                        mailClientView.doGetEmailDetails(
                            $(event.currentTarget).data().emailId,
                            folderAlias
                        );
                    }
                });

                // make table sortable
                if (this.isSortingNotApplied &&
                    0 !== $table.find('tbody tr').length) {
                    // add tablesorter filter for ouy specific date format {
                    $.tablesorter.addParser({
                        id: "customDate",
                        is: function (s) {
                            //24.01.2006 01:30 would also be matched
                            return /\d{1,2}.\d{1,2}.\d{1,4} \d{1,2}:\d{1,2}/.test(s);
                        },
                        format: function (s) {
                            s = s.match(/(\d+)\.(\d+)\.(\d+) (\d+):(\d+)/);

                            return $.tablesorter.formatFloat(new Date(s[3], s[2] - 1, s[1], s[4], s[5], 0).getTime());
                        },
                        type: "numeric"
                    });
                    // add tablesorter filter for ouy specific date format }

                    // init table sorter
                    $table.tablesorter({
                        sortInitialOrder: 'desc',
                        sortList: [
                            [2, 0],
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
                        $table.find('th:eq(' + $(this).index() + ')').click();
                    });

                    setTimeout(function () {
                        mailClientView.$('#' + folderId + ' .ml-list').mCustomScrollbar({autoDraggerLength:false, updateOnContentResize: true});
                    }, 0);

                    this.isSortingNotApplied = false; // we upply sorting, so let other see it
                } else {
                    $('#' + this.mailClientIncomeFolderListId + ' table').trigger('update');
                }
            },

            /**
             * Renders current folder
             *
             * @method
             */
            renderActiveFolder: function () {
                console.log('10');
                var mailClientView = this;

                console.log(this, mailClientView);

                mailClientView.doRenderFolder(mailClientView.mailClient.getActiveFolder().alias);
            },

            /**
             * @method
             */
            renderInboxFolder: function () {
                this.unhideFoldersBlock();

                // set HTML skeleton {
                var skeleton = _.template(income_folder_skeleton_template, {
                    listId: this.mailClientIncomeFolderListId,
                    emailPreviewId: this.mailClientInboxFolderEmailPreviewId
                });

                this.$('#' + this.mailClientContentBlockId).html(skeleton);
                // set HTML skeleton }

                this.updateInboxListView();

                // render preview email
                if (undefined !== this.mailClient.activeEmail) {
                    this.doGetEmailDetails(this.mailClient.activeEmail.mySqlId, this.mailClient.aliasFolderInbox);
                }

                this.renderIcons(this.mailClient.iconsForInboxScreenArray);
                this.mailClient.setActiveScreen(this.mailClient.screenInboxList);


            },

            /**
             * @method
             */
            renderTrashFolder: function () {
                this.unhideFoldersBlock();

                // set HTML skeleton {
                var skeleton = _.template(trash_folder_sceleton, {
                    listId: this.mailClientIncomeFolderListId,
                    emailPreviewId: this.mailClientInboxFolderEmailPreviewId
                });

                this.$('#' + this.mailClientContentBlockId).html(skeleton);
                // set HTML skeleton }

                this.updateTrashListView();

                // render preview email
                if (undefined !== this.mailClient.activeEmail) {
                    this.doGetEmailDetails(this.mailClient.activeEmail.mySqlId, this.mailClient.aliasFolderTrash);
                }

                this.renderIcons(this.mailClient.iconsForTrashScreenArray);
                this.mailClient.setActiveScreen(this.mailClient.screenTrashList);
            },

            /**
             * @method
             */
            renderSendFolder: function () {
                this.unhideFoldersBlock();

                // set HTML skeleton {
                var skeleton = _.template(mail_sender_folder_sceleton_template, {
                    listId: this.mailClientIncomeFolderListId,
                    emailPreviewId: this.mailClientInboxFolderEmailPreviewId
                });

                this.$('#' + this.mailClientContentBlockId).html(skeleton);
                // set HTML skeleton }

                this.updateSendListView();

                // render preview email
                if (undefined !== this.mailClient.activeEmail) {
                    this.doGetEmailDetails(this.mailClient.activeEmail.mySqlId, this.mailClient.aliasFolderSended);
                }

                this.renderIcons(this.mailClient.iconsForSendedScreenArray);

                // this dublicates model code, but this is first step to use models like data storage only

                this.updateFolderLabels();

                this.mailClient.setActiveScreen(this.mailClient.screenSendedList);
            },

            /**
             * @method
             */
            renderDraftsFolder: function () {
                this.unhideFoldersBlock();

                // set HTML skeleton {
                var skeleton = _.template(mail_sender_folder_sceleton_template, {
                    listId: this.mailClientIncomeFolderListId,
                    emailPreviewId: this.mailClientInboxFolderEmailPreviewId
                });

                this.$('#' + this.mailClientContentBlockId).html(skeleton);
                // set HTML skeleton }

                this.updateDraftsListView();

                // render preview email
                if (undefined !== this.mailClient.activeEmail) {
                    this.doGetEmailDetails(this.mailClient.activeEmail.mySqlId, this.mailClient.aliasFolderDrafts);
                }

                this.renderIcons(this.mailClient.iconsForDraftsScreenArray);

                // this dublicates model code, but this is first step to use models like data storage only

                this.updateFolderLabels();

                this.mailClient.setActiveScreen(this.mailClient.screenDraftsList);

            },

            /**
             * @method
             * @param email
             * @param id
             * @param height
             */
            renderEmailPreviewScreen: function (email, id, height) {
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
                    isReadedCssClass: email.getIsReadedCssClass(),
                    attachmentFileName: attachmentLabel,
                    attachmentId: attachmentId,
                    height: height
                });

                this.$('#' + id).html(emailPreviewTemplate);

                this.renderPreviousMessage(email.previouseEmailText);
            },

            /**
             * @method
             * @param email
             */
            renderReadEmail: function (email) {
                // set HTML skeleton {
                var skeleton = _.template(read_mail_sceleton, {
                    emailPreviewId: this.mailClientReadEmailContentBoxId
                });

                $('#' + this.mailClientContentBlockId).html(skeleton);
                // set HTML skeleton}
                this.renderEmailPreviewScreen(email, this.mailClientReadEmailContentBoxId, '350px');
                this.mailClient.setActiveScreen(this.mailClient.screenReadEmail);
            },

            /**
             * @method
             * @param iconButtonAliaces
             */
            renderIcons: function (iconButtonAliaces) {
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
                });
                // choose icons to show }

                // conpose HTML code {
                // declarate action_icon just avoid long strings
                var action_icon = mail_client_action_template;

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
                if (addButtonAddToPlan) {
                    iconsListHtml += _.template(action_icon, {
                        iconCssClass: this.mailClient.aliasButtonAddToPlan,
                        label: 'запланировать'
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

                this.delegateEvents();
            },

            /**
             * @method
             */
            doMoveToTrashActiveEmail: function () {
                if (undefined === this.mailClient.activeEmail) {
                    throw 'try to delete unexistent email';
                }
                this.doMoveToTrash(this.mailClient.activeEmail);
            },

            /**
             * @method
             * @param email
             */
            doMoveToTrash: function (email) {
                var me = this;

                SKApp.server.api(
                    'mail/move',
                    {
                        folderId: this.mailClient.codeFolderTrash,
                        messageId: email.mySqlId
                    },
                    function () {
                    },
                    false
                );

                var updateFolderRender = function () {
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
                };

                this.mailClient.getTrashFolderEmails(
                    this.mailClient.getInboxFolderEmails(
                        updateFolderRender
                    )
                );
            },

            doMoveToInboxByClick: function () {
                if (undefined !== typeof this.mailClient.activeEmail) {
                    this.doMoveToInbox(this.mailClient.activeEmail);
                }
            },

            /**
             * @method
             * @param email
             */
            doMoveToInbox: function (email) {
                var me = this;

                SKApp.server.api(
                    'mail/move',
                    {
                        folderId: this.mailClient.codeFolderInbox,
                        messageId: email.mySqlId
                    },
                    function () {
                    },
                    false
                );

                var updateFolderRender = function () {
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
                }

                this.mailClient.getInboxFolderEmails(
                    this.mailClient.getTrashFolderEmails(
                        updateFolderRender
                    )
                );
            },

            /**
             * @method
             */
            hideFoldersBlock: function () {
                $("#" + this.mailClientScreenID + " header nav").hide();
                $("#" + this.mailClientContentBlockId).css('margin-left', '-180px');
            },

            /**
             * @method
             */
            unhideFoldersBlock: function () {
                $("#" + this.mailClientContentBlockId).css('margin-left', '0px');
                $("#" + this.mailClientScreenID + " header nav").show();
            },

            /**
             * @method
             */
            renderWriteCustomNewEmailScreen: function () {
                this.mailClient.newEmailUsedPhrases = [];
                this.mailClient.availableSubjects = [];
                var mailClientView = this;

                if (0 === this.mailClient.defaultRecipients.length) {
                    this.mailClient.updateRecipientsList();
                }

                // get template
                var htmlSceleton = _.template(mail_client_new_email_template, {});

                this.hideFoldersBlock();

                // render HTML sceleton
                this.$("#" + this.mailClientContentBlockId).html(htmlSceleton);

                this.renderIcons(this.mailClient.iconsForWriteEmailScreenArray);


                this.updateSubjectsList();
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
                    mailClientView.$("#MailClient_NewLetterAttachment div.list").ddslick({
                        data: attachmentsListHtml,
                        width: '100%',
                        selectText: "Нет вложения.",
                        imagePosition: "left"
                    });
                });

                // add attachments list }

                this.$("#MailClient_RecipientsList").tagHandler({
                    className: 'tagHandler recipients-list-widget',
                    availableTags: SKApp.simulation.mailClient.getFormatedCharacterList(),
                    autocomplete: true,
                    allowAdd: false,
                    msgNoNewTag: "Вы не можете написать письмо данному получателю",
                    onAdd: function (tag) {
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
                    },
                    afterDelete: function (tag) {
                        /*if(this.currentRecipients !== undefined && this.currentRecipients.indexOf(tag) === 0) {
                            SKApp.simulation.mailClient.reloadSubjects(mailClientView.getCurrentEmailRecipientIds());
                        }*/
                    },
                    afterAdd: function (tag) {
                        if(mailClientView.$("#MailClient_RecipientsList li.tagItem").get().length == 1) {
                            mailClientView.$("#mailEmulatorNewLetterText").html('');
                            SKApp.simulation.mailClient.reloadSubjects(mailClientView.getCurrentEmailRecipientIds());
                        }

                    },
                    onDelete: function (tag) {
                        mailClientView.currentRecipients = $("#MailClient_RecipientsList li.tagItem").map(function() {
                            return $(this).text();
                        }).get();
                        var me = this;
                        var del = SKApp.simulation.mailClient.reloadSubjectsWithWarning(
                            mailClientView.getCurrentEmailRecipientIds(),
                            'delete',
                            undefined,
                            function () {
                                $("#MailClient_RecipientsList")[0].removeTag(me);
                            },
                            me,
                            function(){
                                SKApp.simulation.mailClient.reloadSubjects(mailClientView.getCurrentEmailRecipientIds());
                                mailClientView.updateSubjectsList();
                            }
                        );
                        return del;
                    }
                });

                this.$('#MailClient_RecipientsList input').focus();
                this.$('#MailClient_RecipientsList input').blur();

                // add IDs to lists of recipients and copies - to simplify testing
                this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(0)').find('a'));

                // fills copyTo list
                this.$("#MailClient_CopiesList").tagHandler({
                    className: 'tagHandler copy-list-widget',
                    availableTags: mailClientView.mailClient.getFormatedCharacterList(),
                    autocomplete: true,
                    allowAdd: false,
                    msgNoNewTag: "Вы не можете написать письмо данному получателю"
                });

                this.$('#MailClient_CopiesList input').focus();
                this.$('#MailClient_CopiesList input').blur();

                // add IDs to lists of recipients and copies - to simplify testing
                this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(1)').find('a'));


                this.delegateEvents();

                this.mailClient.setActiveScreen(this.mailClient.screenWriteNewCustomEmail);

                this.mailClient.setWindowsLog('mailNew');
            },

            /**
             * @method
             * @param elements
             */
            updateIdsForCharacterlist: function (elements) {
                var me = this;
                // items appended to body, so this.$ not works
                $(elements).each(function () {
                    var character = me.mailClient.getRecipientByName($(this).text());
                    $(this).attr('data-character-id', character.excelId);
                });
            },

            /**
             * @method
             * @returns {Array}
             */
            getCurrentEmailRecipientIds: function () {
                var list = [];
                var defaultRecipients = this.mailClient.defaultRecipients; // just to keep code shorter
                var valuesArray = this.$("#MailClient_RecipientsList li.tagItem").get();

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

            /**
             * @method
             * @returns {Array}
             */
            getCurentEmailCopiesIds: function () {
                var list = [];
                var defaultRecipients = this.mailClient.defaultRecipients; // just to keep code shorter

                var valuesArray = $("#MailClient_CopiesList li").get();

                for (var i in valuesArray) {
                    for (var j in defaultRecipients) {
                        // get IDs of character by label text comparsion
                        if ($(valuesArray[i]).text() === defaultRecipients[j].getFormatedForMailToName() && $(valuesArray[i]).text() !== "") {
                            list.push(defaultRecipients[j].mySqlId);
                            break;
                        }
                    }
                }

                return list;
            },

            /**
             * @method
             */
            updateSubjectsList: function () {
                /*
                var subjects = this.mailClient.availableSubjects; // to keep code shorter
                var listHtml = '<option value="0"></option>';

                for (var i in subjects) {
                    listHtml += '<option value="' + subjects[i].characterSubjectId + '">' + subjects[i].getText() + '</option>';
                }

                this.$("#MailClient_NewLetterSubject select").html(listHtml);
                if (subjects.length === 1) {
                    this.$("#MailClient_NewLetterSubject select")[0].selectedIndex = 1;
                    this.doUpdateMailPhrasesList();
                }*/

                var subjects_list = [];
                for (var i in this.mailClient.availableSubjects) {
                    subjects_list.push({
                        text: this.mailClient.availableSubjects[i].text,
                        value: parseInt(this.mailClient.availableSubjects[i].characterSubjectId)
                    });
                }
                if(subjects_list.length == 0){
                    subjects_list.push({
                        text: "без темы.",
                        value: 0,
                        selected: true
                    });
                }
                this.$("#MailClient_NewLetterSubject").ddslick('destroy');
                //this.$("#MailClient_NewLetterSubject").html('');
                var me = this;
                this.$("#MailClient_NewLetterSubject").ddslick({
                    data: subjects_list,
                    width: '100%',
                    selectText: "Нет темы.",
                    imagePosition: "left",
                    onSelected: function () {
                        me.doUpdateMailPhrasesList();
                    }
                });
                if(subjects_list.length == 1){
                    this.$("#MailClient_NewLetterSubject").ddslick('select', {'index':0 });
                }
                if(this.mailClient.activeScreen !== 'SCREEN_WRITE_NEW_EMAIL'){
                    this.$("#MailClient_NewLetterSubject").ddslick('disable');
                }

            },

            /**
             * @method
             * @param {SKMailSubject} subject
             */
            renderSingleSubject: function (subject) {
                var subjects_list = [];
                subjects_list.push({
                    text: subject.getText(),
                    value: subject.characterSubjectId,
                    selected: true
                });
                var me = this;
                this.$("#MailClient_NewLetterSubject").ddslick({
                    data: subjects_list,
                    width: '100%',
                    imagePosition: "left",
                    onSelected: function () {
                    me.doUpdateMailPhrasesList();
                }
                });
                if(this.mailClient.activeScreen !== 'SCREEN_WRITE_NEW_EMAIL'){
                    this.$("#MailClient_NewLetterSubject").ddslick('disable');
                }
            },

            /**
             * @method
             * @returns {*}
             */
            getCurentEmailSubjectId: function () {
                console.log('value: ', this.$("#MailClient_NewLetterSubject input.dd-selected-value"));
                console.log('value: ', this.$("#MailClient_NewLetterSubject input.dd-selected-value").val());
                return this.$("#MailClient_NewLetterSubject input.dd-selected-value").val();
            },

            /**
             * @method
             * @returns {*}
             */
            getCurentEmailSubjectText: function () {
                console.log('text: ', this.$("#MailClient_NewLetterSubject label.dd-selected-text"));
                console.log('text: ', this.$("#MailClient_NewLetterSubject label.dd-selected-text").text());
                return this.$("#MailClient_NewLetterSubject label.dd-selected-text").text();
            },

            /**
             * @method
             */
            renderPhrases: function () {
                var phrases = this.mailClient.availablePhrases;
                var addPhrases = this.mailClient.availableAdditionalPhrases;

                var mainPhrasesHtml = '';
                var additionalPhrasesHtml = '';


                phrases.forEach(function (phrase) {
                    mainPhrasesHtml += _.template(mail_client_phrase_template, {
                        phraseUid: phrase.uid,
                        phraseId: phrase.mySqlId,
                        text: phrase.text
                    });
                });

                addPhrases.forEach(function (phrase) {
                    additionalPhrasesHtml += _.template(mail_client_phrase_template, {
                        phraseUid: phrase.uid,
                        phraseId: phrase.mySqlId,
                        text: phrase.text
                    });
                });

                $("#mailEmulatorNewLetterTextVariants").html(mainPhrasesHtml);
                $("#mailEmulatorNewLetterTextVariantsAdd").html(additionalPhrasesHtml);
                this.$('#mailEmulatorNewLetterText').sortable();


                // some letter has predefine text, update it
                // if there is no text - this.mailClient.messageForNewEmail is empty string
                this.mailClient.newEmailUsedPhrases = [];
                this.renderTXT(this.mailClient.messageForNewEmail);

                this.delegateEvents();
            },

            /**
             * @method
             * @param event
             */
            doAddPhraseToEmail: function (event) {
                event.preventDefault();
                var phrase = this.mailClient.getAvailablePhraseByMySqlId($(event.currentTarget).data('id'));

                if (undefined === phrase) {
                    throw 'Undefined phrase id.';
                }

                // simplest way to clone small object in js {
                var phraseToAdd = new SKMailPhrase; // generate unique uid
                phraseToAdd.mySqlId = phrase.mySqlId;
                phraseToAdd.text = phrase.text;
                // simplest way to clone small object in js }

                // ADD:
                this.mailClient.newEmailUsedPhrases.push(phraseToAdd);

                // render updated state
                this.renderAddPhraseToEmail(phraseToAdd);
            },

            /**
             * @method
             * @param phrase
             */
            renderAddPhraseToEmail: function (phrase) {
                var phraseHtml = _.template(phrase_item, {
                    phraseUid: phrase.uid,
                    phraseId: phrase.mySqlId,
                    text: phrase.text
                });

                $("#mailEmulatorNewLetterText").append(phraseHtml);

                this.delegateEvents();
            },

            /**
             * @method
             * @param event
             */
            doRemovePhraseFromEmail: function (event) {
                event.preventDefault();
                var phrase = this.mailClient.getUsedPhraseByUid($(event.currentTarget).data('uid'));

                if (undefined === phrase) {
                    // if a have seweral (2,3,4...) phrases added to email - click handled twise
                    // currently I ignore this bug.
                    // @todo: fix it
                    throw 'Undefined phrase uid.';
                }

                this.removePhraseFromEmail(phrase);

                var phrases = this.mailClient.newEmailUsedPhrases;
                for (var i in phrases) {
                    // keep '==' not strict!
                    if (phrases[i].uid == phrase.uid) {
                        phrases.splice(i, 1);
                    }
                }
            },

            /**
             * @method
             * @param phrase
             */
            removePhraseFromEmail: function (phrase) {
                this.$("#mailEmulatorNewLetterText li[data-uid=" + phrase.uid + "]").remove();
            },

            /**
             * @method
             * @return SKAttachment | undefined
             */
            getCurrentEmailAttachment: function () {
                var selectedAttachmentLabel = this.$('#MailClient_NewLetterAttachment .dd-selected label').text();
                var attachments = this.mailClient.availableAttachments;

                if (undefined !== selectedAttachmentLabel && null !== selectedAttachmentLabel) {
                    for (var i in attachments) {
                        if (selectedAttachmentLabel === attachments[i].label) {
                            return attachments[i];
                        }
                    }
                }

                return undefined;
            },

            /**
             * @method
             * @return integer | empty string
             */
            getCurrentEmailAttachmentFileId: function () {
                var file = this.getCurrentEmailAttachment();

                if (undefined === file) {
                    return '';
                } else {
                    return file.fileMySqlId;
                }
            },

            /**
             * @method
             * @returns {Array}
             */
            getCurrentEmailPhraseIds: function () {
                var list = [];

                var usedPhrases = $("#mailEmulatorNewLetterText li").get();

                for (var i in usedPhrases) {
                    list.push($(usedPhrases[i]).data('id'));
                }

                return list;
            },

            /**
             * @method
             * @returns {SKEmail}
             */
            generateNewEmailObject: function () {
                var emailToSave = new SKEmail();

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
                subject.characterSubjectId = this.getCurentEmailSubjectId();
                subject.text = this.getCurentEmailSubjectText();
                emailToSave.subject = subject;

                // attachment
                emailToSave.attachment = this.getCurrentEmailAttachment();

                // phrases
                var phrases = this.getCurrentEmailPhraseIds();
                emailToSave.phrases = [];
                for (var i in phrases) {
                    emailToSave.phrases.push(this.mailClient.getAvailablePhraseByMySqlId(phrases[i]));
                }

                // update
                emailToSave.updateStatusPropertiesAccordingObjects();

                return emailToSave;
            },

            /**
             * @method
             */
            doSaveEmailToDrafts: function () {
                var me = this;
                var emailToSave = this.generateNewEmailObject();

                this.mailClient.saveToDraftsEmail(emailToSave, function () {
                    me.updateFolderLabels();
                    me.renderActiveFolder();

                    me.mailClient.setWindowsLog(
                        'mailMain',
                        me.mailClient.getActiveEmailId()
                    );
                });
            },

            /**
             * @method
             */
            doSendEmail: function () {
                var emailToSave = this.generateNewEmailObject();
                var mailClientView = this;

                this.mailClient.sendNewCustomEmail(emailToSave);
            },

            /**
             * @method
             * @param iconsList
             */
            renderWriteEmailScreen: function (iconsList) {
                var mailClientView = this;

                if (0 === this.mailClient.defaultRecipients.length) {
                    this.mailClient.updateRecipientsList();
                }

                // get template
                var htmlSceleton = _.template(mail_client_new_email_template, {});

                this.hideFoldersBlock();

                // render HTML sceleton
                this.$("#" + this.mailClientContentBlockId).html(htmlSceleton);

                this.renderIcons(this.mailClient.iconsForWriteEmailScreenArray);



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

                    mailClientView.$("#MailClient_NewLetterAttachment div.list").ddslick({
                        data: attachmentsListHtml,
                        width: '100%',
                        selectText: "Нет вложения.",
                        imagePosition: "left"
                    });
                    // add attachments list }

                    mailClientView.delegateEvents();
                });
            },

            /**
             * @method
             */
            doUpdateMailPhrasesList: function () {
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
                                        mailClient.newEmailSubjectId = mailClientView.getCurentEmailSubjectId();
                                        mailClient.getAvailablePhrases(mailClient.newEmailSubjectId);
                                        mailClient.getAvailablePhrases(mailClientView.getCurentEmailSubjectId(), function () {

                                            $('#mailEmulatorNewLetterText').html('');

                                        });
                                        delete mailClient.message_window;
                                    }
                                },
                                {
                                    'value': 'Вернуться',
                                    'onclick': function () {
                                        mailClientView.selectSubjectByValue(mailClient.newEmailSubjectId);
                                        delete mailClient.message_window;
                                    }
                                }
                            ]
                        });
                    }
                } else {
                    // standart way
                    mailClient.newEmailSubjectId = mailClientView.getCurentEmailSubjectId();
                    mailClient.getAvailablePhrases(mailClientView.getCurentEmailSubjectId(), function () {

                        mailClientView.$('#mailEmulatorNewLetterText').html('');

                    });
                }
            },

            /**
             * @method
             * @param value
             */
            selectSubjectByValue: function (value) {
                var index = null;
                this.$("#MailClient_NewLetterSubject li a input").each(function(i, el) {
                    if($(el).val() == value){
                        index = i;
                    }
                });
                if(index === null){
                    throw new Error("index !== null");
                }
                this.$("#MailClient_NewLetterSubject").ddslick('select', {'index': index });
            },

            /**
             * @method
             * @param text
             */
            renderPreviousMessage: function (text) {
                if (undefined !== text && '' !== text) {
                    text = '<pre><p style="color:blue;">' + text + '</p></pre>';
                }
                this.$(".previouse-message-text").html(text);
                this.delegateEvents();
            },

            /**
             * @method
             */
            renderTXT: function () {
                if (undefined !== this.mailClient.messageForNewEmail && '' !== this.mailClient.messageForNewEmail) {
                    $('#mailEmulatorNewLetterText').
                        html(this.mailClient.messageForNewEmail.replace('\n', "<br />", "g").replace('\n\r', "<br />", "g"));
                }
            },

            /**
             * @method
             * @param response
             * @returns {boolean}
             */
            fillMessageWindow: function (response) {
                if (null === response.subjectId) {
                    this.doRenderFolder(this.mailClient.aliasFolderInbox, false);
                    this.renderNullSubjectIdWarning('Вы не можете ответить на это письмо.');
                    return  false;
                }

                this.mailClient.messageForNewEmail = response.phrases.message;

                this.renderWriteEmailScreen(this.mailClient.iconsForWriteEmailScreenArray);

                var subject = new SKMailSubject();
                subject.text = response.subject;
                subject.mySqlId = response.subjectId;
                subject.characterSubjectId = response.subjectId;
                this.parentSubject = subject;
                this.renderSingleSubject(subject);

                this.renderPreviousMessage(response.phrases.previouseMessage);

                this.renderTXT();

                // even if there is one recipient,but it must be an array
                var recipient = [SKApp.simulation.mailClient.getRecipientByMySqlId(response.receiver_id)
                    .getFormatedForMailToName()];

                this.$("#MailClient_RecipientsList .tagInput").remove(); // because "allowEdit:false"
                // set recipients
                this.$("#MailClient_RecipientsList").tagHandler({
                    className: 'tagHandler recipients-list-widget',
                    assignedTags: recipient,
                    availableTags: recipient,
                    allowAdd: false,
                    allowEdit: false
                });

                this.$('#MailClient_RecipientsList').focus();
                this.$('#MailClient_RecipientsList').blur();

                // add IDs to lists of recipients and copies - to simplify testing
                this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(0)').find('a'));

                // add copies if they exests {
                var copies = [];
                if (undefined !== response.copiesIds) {
                    var ids = response.copiesIds.split(',');
                    for (var i in ids) {
                        if (0 < parseInt(ids[i])) {
                            copies.push(SKApp.simulation.mailClient.getRecipientByMySqlId(parseInt(ids[i]))
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

                this.$('#MailClient_CopiesList').focus();
                this.$('#MailClient_CopiesList').blur();

                // add IDs to lists of recipients and copies - to simplify testing
                this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(1)').find('a'));

                // prevent custom text input
                this.$("#MailClient_RecipientsList input").attr('readonly', 'readonly');
                this.$("#MailClient_CopiesList input").attr('readonly', 'readonly');
                // add copies if they exests }

                // add phrases {
                SKApp.simulation.mailClient
                    .setRegularAvailablePhrases(response.phrases.data);

                SKApp.simulation.mailClient
                    .setAdditionalAvailablePhrases(response.phrases.addData);

                this.renderPhrases();
                // add phrases }

            },

            /**
             * @method
             * @param message
             */
            renderNullSubjectIdWarning: function (message) {
                var mailClientView = this;

                mailClientView.message_window = new SKDialogView({
                    'message': message,
                    'buttons': [
                        {
                            'value': 'Окей',
                            'onclick': function () {
                                delete mailClientView.message_window;
                            }
                        }
                    ]
                });
            },

            /**
             * @method
             * @param {Object} response API response
             */
            doUpdateScreenFromForwardEmailData: function (response) {

                if (1 == response.result) {
                    if (null == response.subjectId) {
                        this.doRenderFolder(this.mailClient.aliasFolderInbox, false);
                        this.renderNullSubjectIdWarning('Вы не можете переслать это письмо.');
                        return  false;
                    }

                    this.renderWriteEmailScreen(this.mailClient.iconsForWriteEmailScreenArray);

                    var subject = new SKMailSubject();
                    subject.text = response.subject;
                    subject.mySqlId = response.subjectId;
                    subject.parentMySqlId = response.parentSubjectId;
                    subject.characterSubjectId = response.subjectId;

                    this.renderSingleSubject(subject);

                    this.renderPreviousMessage(response.phrases.previouseMessage);
                    var me = this;
                    // set recipients
                    $("#MailClient_RecipientsList").tagHandler({
                        className: 'tagHandler recipients-list-widget',
                        availableTags: SKApp.simulation.mailClient.getFormatedCharacterList(),
                        autocomplete: true,
                        onAdd: function (tag) {
                            var add = SKApp.simulation.mailClient.reloadSubjectsWithWarning(
                                me.getCurrentEmailRecipientIds(),
                                'add_fwd',
                                subject,
                                function () {
                                    $("#MailClient_RecipientsList")[0].addTag(tag);
                                }
                            );
                            return add;
                        },
                        afterDelete: function (tag) {
                            //
                        },
                        afterAdd: function (tag) {
                            SKApp.simulation.mailClient.reloadSubjects(me.getCurrentEmailRecipientIds(), subject);
                            SKApp.simulation.mailClient.getAvailablePhrases(SKApp.simulation.mailClient.availableSubjects[0].characterSubjectId);
                        },
                        onDelete: function (tag) {
                            var el = this;
                            var del = SKApp.simulation.mailClient.reloadSubjectsWithWarning(
                                me.getCurrentEmailRecipientIds(),
                                'delete_fwd',
                                undefined,
                                function () {
                                    //$("#MailClient_RecipientsList").appand('<li class="tagItem">'+tag+'</li>');
                                    $("#MailClient_RecipientsList")[0].removeTag(el);
                                },
                                me
                            );
                            return del;
                        }
                    });

                    this.$('#MailClient_RecipientsList').focus();
                    this.$('#MailClient_RecipientsList').blur();

                    // add IDs to lists of recipients and copies - to simplify testing
                    this.updateIdsForCharacterlist($('ul.ui-autocomplete:eq(0)').find('a'));

                    $("#MailClient_CopiesList").tagHandler({
                        className: 'tagHandler copy-list-widget',
                        availableTags: SKApp.simulation.mailClient.getFormatedCharacterList(),
                        autocomplete: true
                    });

                    this.$('#MailClient_CopiesList').focus();
                    this.$('#MailClient_CopiesList').blur();

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
                    throw "Can`t initialize responce email. View. #2";
                }
            },

            /**
             * @method
             */
            renderReplyScreen: function () {
                this.mailClient.newEmailUsedPhrases = [];

                var response = this.mailClient.getDataForReplyToActiveEmail();

                // strange, sometimes responce return to lile JSON but like some response object
                // so we get JSON from it {
                if (undefined == response.result && undefined !== response.responseText) {
                    response = $.parseJSON(response.responseText);
                }
                // so we get JSON from it }

                if (false !== this.fillMessageWindow(response)) {
                    this.mailClient.setActiveScreen(this.mailClient.screenWriteReply);
                    this.mailClient.setWindowsLog('mailNew');
                }
            },

            /**
             * @method
             */
            renderReplyAllScreen: function () {
                this.mailClient.newEmailUsedPhrases = [];

                var response = this.mailClient.getDataForReplyAllToActiveEmail();

                // strange, sometimes response return to JSON but like some response object
                // so we get JSON from it {
                if (undefined == response.result && undefined !== response.responseText) {
                    response = $.parseJSON(response.responseText);
                }
                // so we get JSON from it }

                if (false !== this.fillMessageWindow(response)) {
                    this.mailClient.setActiveScreen(this.mailClient.screenWriteReplyAll);
                    this.mailClient.setWindowsLog('mailNew');
                }
            },

            /**
             * @method
             */
            renderForwardEmailScreen: function () {
                this.mailClient.newEmailUsedPhrases = [];

                var response = this.mailClient.getDataForForwardActiveEmail();
                // strange, sometimes responce return to lile JSON but like some response object
                // so we get JSON from it {
                if (undefined == response.result && undefined !== response.responseText) {
                    response = $.parseJSON(response.responseText);
                }
                // so we get JSON from it }
                if (false !== this.doUpdateScreenFromForwardEmailData(response)) {
                    this.mailClient.setActiveScreen(this.mailClient.screenWriteForward);
                    this.mailClient.setWindowsLog('mailNew');
                }
            },

            /**
             * @method
             */
            doSendDraft: function () {
                var me = this;
                SKApp.server.api(
                    'mail/sendDraft',
                    {
                        id: this.mailClient.activeEmail.mySqlId
                    },
                    function (response) {
                        if (1 !== response.result) {
                            // display message for user
                            SKApp.simulation.mailClient.message_window =
                                SKApp.simulation.mailClient.message_window || new SKDialogView({
                                    'message': 'Не удалось отправить черновик адресату.',
                                    'buttons': [
                                        {
                                            'value': 'Окей',
                                            'onclick': function () {
                                                delete SKApp.simulation.mailClient.message_window;
                                            }
                                        }
                                    ]
                                });
                        } else {
                            me.mailClient.setWindowsLog(
                                'mailMain',
                                me.mailClient.getActiveEmailId()
                            );
                        }

                        me.mailClient.getDraftsFolderEmails(function () {
                            me.mailClient.getSendedFolderEmails();
                            // get first email if email exist in folder {
                            var draftEmails = me.mailClient.getDraftsFolder().emails;

                            SKApp.simulation.mailClient.activeEmail = undefined;
                            for (var i in draftEmails) {
                                SKApp.simulation.mailClient.activeEmail = draftEmails[i];
                            }
                            // get first email if email exist in folder }

                            me.renderDraftsFolder();
                        });
                    });

            },
            onMailSent: function () {
                this.updateFolderLabels();
                this.renderActiveFolder();

                this.mailClient.setWindowsLog(
                    'mailMain',
                    this.mailClient.getActiveEmailId()
                );
            }
        });

    return SKMailClientView;
});
