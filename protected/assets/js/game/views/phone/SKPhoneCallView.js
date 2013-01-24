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
            var me;
            this.listenTo(this.options.model_instance.get('sim_event'),'complete', function () {
                this.options.model_instance.close();
            });
            SKWindowView.prototype.initialize.call(this);
        },
        renderContent: function (window_el) {
            window_el.html(_.template($('#Phone_Call').html(), {call: this.options.event.get('data')}));
        },
        getMenu: function(event){
            var id = $(event.currentTarget).attr('window_id');
            this.renderTPL('#'+id+' .phone-screen', '#Phone_Menu', {windowID:id});
        },
        getCountViews: function(){
            return $('.'+this.windowClass).length;
        },
        reply: function(event) {
            var dialogId = $(event.currentTarget).attr('data-dialog-id');
            var me = this;
            this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
            });
        },
        noReply: function(event) {
            var dialogId = $(event.currentTarget).attr('data-dialog-id');
            var me = this;
            this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
            });
        }
   });         
});
