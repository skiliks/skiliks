/*global SKWindow, SKApp, _, SKWindowView
 */

$(function () {
    "use strict";

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
        renderContent: function (window_el) {
            window_el.html(_.template($('#Phone_Call').html(), {call: this.options.event.get('data')}));
        },
        getMenu: function(event){
            var id = $(event.toElement).attr('window_id');
            this.renderTPL('#'+id+' .phone-screen', '#Phone_Menu', {windowID:id});
        },
        getCountViews: function(){
            return $('.'+this.windowClass).length;
        },
        reply: function(event) {
            var dialogId = $(event.toElement).attr('data-dialog-id');
            this.options.model_instance.close();
            SKApp.server.api('dialog/get', {'dialogId':dialogId}, function (data) {
                SKApp.user.simulation.parseNewEvents(data.events);
            });
        },
        noReply: function(event) {
            var dialogId = $(event.toElement).attr('data-dialog-id');
            this.options.model_instance.close();
            SKApp.server.api('dialog/get', {'dialogId':dialogId, 'time':SKApp.user.simulation.getGameMinutes()}, function (data) {
                SKApp.user.simulation.parseNewEvents(data.events);
            });
        }
   });         
});
