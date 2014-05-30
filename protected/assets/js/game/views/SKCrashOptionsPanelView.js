/*global Backbone, _, define, $, SKApp */

var SKCrashOptionsPanelView;
define([
    "game/views/SKDialogView",
    "text!game/jst/world/crash.jst"
], function (SKDialogView, dialog_template) {
    "use strict";
    /**
     * List of user's phrases added to letter
     * @class SKCrachOptionsPanelView
     * @augments Backbone.View
     */
    SKCrashOptionsPanelView = SKDialogView.extend({

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        'events': {
            'click .crash.close-mail': 'doCloseMail',
            'click .crash.close-phone': 'doClosePhone',
            'click .crash.close-plan': 'doClosePlan',
            'click .crash.close-my_documents': 'doCloseMyDocuments',
            'click .crash.close-documents': 'doCloseDocuments',
            'click .crash.close-visit': 'doCloseVisit',
            'click .crash.stop-sim': 'doStopSimulation',
            'click .crash.restore-events': 'doRestoreEvents',
            'click .crash.show-replicas': 'doShowReplicas',
            'click .mail-popup-button': 'handleClick',
            'click .dialog-close': 'doDialogClose'
        },

        /**
         * Стандартный родительский метод
         */
        render: function () {
            try {
                this.logAction('Open crash options dialog');

                var me = this;

                if (this.options.modal !== false) {
                    // must be first to get Z-index under dialog HTML block
                    this.renderPreventClickElement();
                }

                this.$el = $(_.template(dialog_template, {
                    cls: this.options.class,
                    title: this.options.message,
                    content: this.options.content,
                    buttons: this.options.buttons,
                    addCloseButton: me.addCloseButton
                }));

                this.$el.css({
                    'top': '70px',
                    'position': 'absolute',
                    'width': '100%',
                    'margin': 'auto'
                });
                me.$el.topZIndex();

                if ($('.windows-container').length) {
                    $('.windows-container').prepend(this.$el);
                } else {
                    $('body').append(this.$el);
                }

                if (me.isPutCenter) {
                    me.center();
                }

            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Стандартный родительский метод
         */
        remove: function() {
            try {
                SKApp.server.api('simulation/emergencyClosed', {}, function () {});
                SKDialogView.prototype.remove.apply(this, arguments);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Метод закрытия почтовика
         * @param OnClickEvent e
         * @returns {boolean}
         */
        doCloseMail:function(e){
            this.logAction($(e.currentTarget).text());
            var mailEmulators = SKApp.simulation.window_set.where({name: "mailEmulator"});
            $.each(mailEmulators, function(index, mail){
                mail.setOnTop();
                mail.close();
            });
            this.remove();
            return false;
        },

        /**
         * Метод закрытия телефона
         * @param OnClickEvent e
         * @returns {boolean}
         */
        doClosePhone:function(e){
            this.logAction($(e.currentTarget).text());
            var phones = SKApp.simulation.window_set.where({name: "phone"});
            $.each(phones, function(index, phone){
                phone.setOnTop();
                phone.close();
            });
            $.each(SKApp.simulation.events.models, function(index, event) {
                if(event.status === 'in progress') {
                    event.setStatus('completed');
                }
            });
            this.remove();
            $('.phone, .door').removeClass('icon-button-disabled');
            return false;
        },

        /**
         * Метод закрытия планировщика
         * @param OnClickEvent e
         * @returns {boolean}
         */
        doClosePlan:function(e){
            this.logAction($(e.currentTarget).text());
            var planners = SKApp.simulation.window_set.where({name: "plan"});
            $.each(planners, function(index, plan){
                plan.setOnTop();
                plan.close();
            });
            this.remove();
            return false;
        },

        /**
         * Метод закрытия папки мои документы
         * @param OnClickEvent e
         * @returns {boolean}
         */
        doCloseMyDocuments:function(e){
            this.logAction($(e.currentTarget).text());
            var my_documents = SKApp.simulation.window_set.where({subname: "documents"});
            $.each(my_documents, function(index, folder){
                folder.setOnTop();
                folder.close();
            });
            this.remove();
            return false;
        },

        /**
         * Метод закрытия документов
         * @param OnClickEvent e
         * @returns {boolean}
         */
        doCloseDocuments:function(e){
            this.logAction($(e.currentTarget).text());
            var documents = SKApp.simulation.window_set.where({name: "documents"});
            $.each(documents, function(index, document) {
                if(document.get('subname') !== 'documents') {
                    document.setOnTop();
                    document.close();
                }
            });
            this.remove();
            return false;
        },

        /**
         * Метод закрытия визитов (звонки, визиты)
         * @param OnClickEvent e
         * @returns {boolean}
         */
        doCloseVisit:function(e){
            this.logAction($(e.currentTarget).text());
            var visitors = SKApp.simulation.window_set.where({name: "visitor"});
            $.each(visitors, function(index, visit) {
                    visit.setOnTop();
                    visit.close();
            });
            if(SKApp.simulation.isPaused()){
                SKApp.simulation.stopPause(function(){});
            }
            $.each(SKApp.simulation.events.models, function(index, event) {
                if(event.status === 'in progress') {
                    event.setStatus('completed');
                }
            });
            this.remove();
            $('.phone, .door').removeClass('icon-button-disabled');
            return false;
        },

        /**
         * Логирование креша игры
         * @param String action
         */
        logAction:function(action) {
            SKApp.server.api('simulation/logCrashAction', {action:action}, function () {});
        },

        /**
         * Метод завершает мимуляцию
         * @param OnClickEvent e
         * @returns {boolean}
         */
        doStopSimulation:function(e) {
            this.logAction($(e.currentTarget).text());
            this.remove();
            SKApp.simulation.onFinishTime();
            return false;
        },

        /**
         * Метод завершает все начатые события
         * @param OnClickEvent e
         * @returns {boolean}
         */
        doRestoreEvents:function(e) {
            this.logAction($(e.currentTarget).text());
            $.each(SKApp.simulation.events.models, function(index, event) {
                if(event.status === 'in progress') {
                    event.setStatus('completed');
                }
            });
            this.remove();
            return false;
        },

        /**
         * Метод отображает все скрытые реплики
         * @param OnClickEvent e
         * @returns {boolean}
         */
        doShowReplicas: function(e) {
            window.netSpeedVerbose = 'slow';

            this.logAction($(e.currentTarget).text());

            $('.visitor-reply').removeClass('hidden');
            $('.char-reply').removeClass('hidden');
            $('.phone-reply-h').removeClass('hidden');

            this.remove();
            return false;
        }
    });

    return SKCrashOptionsPanelView; //SKDialogView;
});