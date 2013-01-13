/*global SKWindow, SKApp
 */

$(function () {
    "use strict";

    window.SKPhoneCallView = window.SKWindowView.extend({
        el:null,
        countMaxView: 1,
        windowClass: "phoneMainDiv",
        windowID: '',
        SKWindow:null,
        events: {
            'click .btn-cl':'close',
            'click .phone_get_menu':'getMenu',
            'click #phone_reply':'reply',
            'click #phone_no_reply':'noReply'
        },
        renderContent: function (window_el) {
            var div = document.createElement('div');
            this.windowID = this.cid;
            div.setAttribute('id', this.windowID);
            div.setAttribute('class', this.windowClass);
            div.style.position = "absolute";
            $(this.container).append(div);
            $('#'+this.windowID).css('position', 'absolute');
            var canvas = $('#canvas').width();
            var icons = $('.main-screen-icons').width();
            var phone = $('#'+this.windowID).width(); 
            var left = (canvas - icons - phone) / 2;
            $('#'+this.windowID).css('left', left+'px');
            this.renderTPL('#'+this.windowID, '#Phone_Call', {windowID:this.windowID, call:call});
            this.$el = $('#'+this.windowID);
            this.SKWindow = new SKWindow({name:'phone', subname:'phoneCall'});
            this.SKWindow.open();
        },
        close: function (event) {            
            if(event !== undefined){
                var id = $(event.toElement).attr('window_id');
                $('#'+id).remove();
            }else{
                $('.'+this.windowClass).remove();
            }
            this.SKWindow.close();
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
            this.close(event);
            SKApp.server.api('dialog/get', {'dialogId':dialogId}, function (data) {
                SKApp.user.simulation.parseNewEvents(data.events);
            });
        },
        noReply: function(event) {
            var dialogId = $(event.toElement).attr('data-dialog-id');
            this.close(event);
            SKApp.server.api('dialog/get', {'dialogId':dialogId, 'time':SKApp.user.simulation.getGameMinutes()}, function (data) {
                SKApp.user.simulation.parseNewEvents(data.events);
            });
        }
   });         
});
