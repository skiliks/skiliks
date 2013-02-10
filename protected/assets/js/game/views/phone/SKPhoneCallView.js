/*global SKWindow, SKApp, _, SKWindowView
 */

$(function () {
    "use strict";
    /**
     * @class
     * @type {*}
     */
    window.SKPhoneCallView = SKWindowView.extend({
        el:null,
        countMaxView: 1,
        windowClass: "phoneMainDiv",
        windowID: '',
        SKWindow:null,
        events:_.defaults({
            'click .phone_get_menu':'getMenu',
            'click #phone_reply':'reply',
            'click #phone_no_reply':'noReply'
        },SKWindowView.events),
        initialize:function () {
            //this.listenTo(this.options.model_instance.get('sim_event'),'complete', function () {
            //    this.options.model_instance.close();
            //});
            var me = this;
            this.listenTo(this.options.model_instance, 'refresh', function () {
                me.render();
            });
            SKWindowView.prototype.initialize.call(this);
        },
        renderContent: function (window_el) {
            var me = this;
            window_el.html(_.template($('#Phone_Call').html(), {call: this.options.event.get('data')}));
            var event = this.options.model_instance.get('sim_event');
            var dialogId = event.get('data')[0].id;
            //me.options.model_instance.setDialog(dialogId);
        },
        getMenu: function(event){
            //Todo: уточнить возможность у Антона
            //var id = $(event.currentTarget).attr('window_id');
            //this.renderTPL('#'+id+' .phone-screen', '#Phone_Menu', {windowID:id});
        },
        getCountViews: function(){
            return $('.'+this.windowClass).length;
        },
        reply: function(event) {
            var dialogId = $(event.currentTarget).attr('data-dialog-id');
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
});
