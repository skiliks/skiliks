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
        'events': {
            'click .crash.close-mail': 'doCloseMail',
            'click .crash.close-phone': 'doClosePhone',
            'click .crash.close-plan': 'doClosePlan',
            'click .crash.close-my_documents': 'doCloseMyDocuments',
            'click .crash.close-documents': 'doCloseDocuments',
            'click .crash.close-visit': 'doCloseVisit',
            'click .mail-popup-button': 'handleClick',
            'click .dialog-close': 'doDialogClose'
        },
        render: function () {
            try {
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
                    //'zIndex': 60000, // topZIndex wokrs well
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
        remove: function() {
            try {
                this.cleanUpDOM();
                this.trigger('close');
                SKApp.simulation.system_options = null;
                return Backbone.View.prototype.remove.call(this);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        doCloseMail:function(e){
            this.logAction($(e.currentTarget).text());
            console.log("close");
            var mailEmulators = SKApp.simulation.window_set.where({name: "mailEmulator"});
            $.each(mailEmulators, function(index, mail){
                mail.setOnTop();
                mail.close();
            });
            this.remove();
            return false;
        },
        doClosePhone:function(e){
            this.logAction($(e.currentTarget).text());
            var phones = SKApp.simulation.window_set.where({name: "phone"});
            $.each(phones, function(index, phone){
                phone.setOnTop();
                phone.close();
            });
            this.remove();
            $('.phone, .door').removeClass('icon-button-disabled');
            return false;
        },
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
        doCloseVisit:function(e){
            this.logAction($(e.currentTarget).text());
            var visitors = SKApp.simulation.window_set.where({name: "visitor"});
            $.each(visitors, function(index, visit) {
                    visit.setOnTop();
                    visit.close();
            });
            this.remove();
            $('.phone, .door').removeClass('icon-button-disabled');
            return false;
        },
        logAction:function(action) {
            SKApp.server.api('simulation/logCrashAction', {action:action}, function () {});
        }
    });
    return SKCrashOptionsPanelView; //SKDialogView;
});