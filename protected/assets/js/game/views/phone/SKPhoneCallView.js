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
     * @type {*}
     */
    SKPhoneCallView = SKWindowView.extend({

        el:null,

        countMaxView: 1,

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

        initialize:function () {
            var me = this;
            this.listenTo(this.options.model_instance, 'refresh', function () {
                me.render();
            });
            SKWindowView.prototype.initialize.call(this);
        },

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

        getMenu: function(event){
            //Todo: уточнить возможность у Антона
            //var id = $(event.currentTarget).attr('window_id');
            //this.renderTPL('<xxx>', {windowID:id});
        },

        getCountViews: function(){
            return $('.'+this.windowClass).length;
        },

        reply: function(event) {
            event.preventDefault();
            event.stopPropagation();
            this.runReply($(event.currentTarget).attr('data-dialog-id'));
        },

        runReply: function(dialogId) {
            var dialogId = dialogId;
            var me = this;
            this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                me.options.model_instance.setLastDialog(dialogId);
                me.options.model_instance.close();
            });
        },

        noReply: function(event) {
            var dialogId = $(event.currentTarget).attr('data-dialog-id');
            var me = this;
            this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                var phone_history = SKApp.user.simulation.phone_history;
                phone_history.fetch();
                me.options.model_instance.setLastDialog(dialogId);
                me.options.model_instance.close();
            });
        }
   });

    return SKPhoneCallView;
});
