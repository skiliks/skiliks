/*global SKWindow, SKApp, _, SKWindowView, $, define
 */

var SKPhoneCallView;

define([
    "text!game/jst/phone/call_template.jst",

    "game/views/SKWindowView"
], function (
    call_template
) {
    "use strict";
    /**
     * @class SKPhoneCallView
     * @augments Backbone.View
     */
    SKPhoneCallView = SKWindowView.extend({

        /**
         * Стандартный родительский метод
         */
        isDisplaySettingsButton:true,

        /**
         * Стандартный родительский метод
         */
        isDisplayCloseButton:false,

        /**
         * Стандартный родительский метод
         */
        title: "Телефон",

        /**
         * Стандартный родительский метод
         */
        windowName:'phone',

        /**
         * Стандартный родительский метод
         */
        addClass: "phone-call",

        /**
         * Стандартный родительский метод
         */
        windowClass: "phoneMainDiv",

        /**
         * @param function deny_timeout_id
         */
        deny_timeout_id: null,

        /**
         * Стандартный родительский метод
         */
        windowID: '',

        /**
         * Стандартный родительский метод
         */
        dimensions: {
            width: 371,
            height: 560
        },

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        events:_.defaults({
            'click #phone_reply'    : 'reply',
            'click #phone_no_reply' : 'noReply'
        },SKWindowView.prototype.events),

        /**
         * Constructor
         * @method initialize
         */
        initialize:function () {
            try {
                SKApp.simulation.trigger('audio-phone-small-zoom-stop');
                var me = this;
                this.listenTo(this.options.model_instance, 'refresh', function () {
                    me.render();
                });
                SKWindowView.prototype.initialize.call(this);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Стандартный родительский метод
         *
         * @param {jQuery} el
         */
        renderContent: function (el) {
            try {
                SKApp.simulation.trigger('audio-phone-call-start');

                var me = this;
                el.html(_.template(call_template, {
                    call: this.options.event.get('data'),
                    isDisplaySettingsButton:this.isDisplaySettingsButton,
                    windowName:this.windowName
                }));
                var event = this.options.model_instance.get('sim_event');
                var dialogId = event.get('data')[0].id;

                if ('undefined' === typeof event.get('data')[2]) {
                    var dialog_1_Id = event.get('data')[1].id; // button "Ответить"

                    var callback = function() {
                        me.runReply(dialog_1_Id);
                    };
                    setTimeout(callback, 2000);
                }
                var noReply = function(){
                    me.doActivate();
                    me.$('#phone_no_reply').click();
                };
                this.deny_timeout_id = setTimeout(noReply, 20000);
                this.listenTo(this.options.model_instance, 'close', function () {
                    clearTimeout(me.deny_timeout_id);
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Обработка события "Ответить"
         *
         * @param OnClickEvent event
         */
        reply: function(event) {
            try {
                event.preventDefault();
                event.stopPropagation();

                var me = this;
                me.trigger('audio-phone-call-stop');

                if ($(event.currentTarget).attr('data-disabled')) {
                    return;
                }
                this.$('.phone-call-in-btn a').each(function () {
                    $(this).attr('data-disabled', true);
                });
                this.runReply($(event.currentTarget).attr('data-dialog-id'));
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param dialogId
         */
        runReply: function(dialogId) {
            try {
                var me = this;
                this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                    me.options.model_instance.setLastDialog(dialogId);
                    me.options.model_instance.close();
                });
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
        noReply: function(event) {
            try {
                var dialogId = $(event.currentTarget).attr('data-dialog-id');
                if ($(event.currentTarget).attr('data-disabled')) {
                    return;
                }
                this.$('.phone-call-in-btn a').each(function () {
                    $(this).attr('data-disabled', true);
                });

                var me = this;
                this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                    var phone_history = SKApp.simulation.phone_history;
                    phone_history.fetch();
                    me.options.model_instance.setLastDialog(dialogId);
                    me.options.model_instance.close();
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
   });

    return SKPhoneCallView;
});
