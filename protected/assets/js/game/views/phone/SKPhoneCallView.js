/*global SKWindow, SKApp, _, SKWindowView
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
        title: "Телефон",
        el:null,

        countMaxView: 1,
        addClass: "phone-call",
        windowClass: "phoneMainDiv",

        windowID: '',

        SKWindow:null,

        dimensions: {
            width: 371,
            height: 560
        },

        events:_.defaults({
            'click .phone_get_menu':'getMenu',
            'click #phone_reply':'reply',
            'click #phone_no_reply':'noReply'
        },SKWindowView.events),

        /**
         * Constructor
         * @method initialize
         */
        initialize:function () {
            var me = this;
            this.listenTo(this.options.model_instance, 'refresh', function () {
                me.render();
            });
            SKWindowView.prototype.initialize.call(this);
        },

        /**
         * @method
         * @param window_el
         */
        renderContent: function (window_el) {
            var me = this;
            window_el.html(_.template(call_template, {call: this.options.event.get('data')}));
            var event = this.options.model_instance.get('sim_event');
            var dialogId = event.get('data')[0].id;

            if (undefined == event.get('data')[2]) {
                var me = this;
                var dialog_1_Id = event.get('data')[1].id; // button "Ответить"

                var callback = function() {
                    me.runReply(dialog_1_Id);
                };
                setTimeout(callback, 2000);
            }
        },

        /**
         * @method
         * @param event
         */
        getMenu: function(event){
            //Todo: уточнить возможность у Антона
            //var id = $(event.currentTarget).attr('window_id');
            //this.renderTPL('<xxx>', {windowID:id});
        },

        /**
         * @method
         * @returns {Number|jQuery}
         */
        getCountViews: function(){
            return $('.'+this.windowClass).length;
        },

        /**
         * @method
         * @param event
         */
        reply: function(event) {
            event.preventDefault();
            event.stopPropagation();
            if ($(event.currentTarget).attr('data-disabled')) {
                return;
            }
            this.$('.phone-call-in-btn a').each(function () {
                $(this).attr('data-disabled', true);
            });
            this.runReply($(event.currentTarget).attr('data-dialog-id'));
        },

        /**
         * @method
         * @param dialogId
         */
        runReply: function(dialogId) {
            var me = this;
            if ($(event.currentTarget).attr('data-disabled')) {
                return;
            }
            this.$('.phone-call-in-btn a').each(function () {
                $(this).attr('data-disabled', true);
            });
            this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                me.options.model_instance.setLastDialog(dialogId);
                me.options.model_instance.close();
            });
        },

        /**
         * @method
         * @param event
         */
        noReply: function(event) {
            var dialogId = $(event.currentTarget).attr('data-dialog-id');
            var me = this;
            this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                var phone_history = SKApp.simulation.phone_history;
                phone_history.fetch();
                me.options.model_instance.setLastDialog(dialogId);
                me.options.model_instance.close();
            });
        }
   });

    return SKPhoneCallView;
});
