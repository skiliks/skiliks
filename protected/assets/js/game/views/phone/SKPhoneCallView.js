/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {
    "use strict";

    window.SKPhoneCallView = window.SKWindowView.extend({
        initialize:function (call) {
            if(this.countMaxView > this.getCountViews()){
                this.open(call);
            } else {
                this.close();
            }
        },
        el:null,
        countMaxView: 1,
        windowClass: "phoneMainDiv",
        windowID: '',
        events: {
            'click .btn-cl':'close',
            'click .phone_get_menu':'getMenu',
            'click #phone_reply':'reply',
            'click #phone_no_reply':'noReply'
        },
        open: function (call) {
            console.log(call);
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
        },
        close:function (event) {            
            if(event != undefined){
                var id = $(event.toElement).attr('window_id');
                $('#'+id).remove();
            }else{
                $('.'+this.windowClass).remove();
            }
            
        },
        getMenu: function(event){
            var id = $(event.toElement).attr('window_id');
            this.renderTPL('#'+id+' .phone-screen', '#Phone_Menu', {windowID:id});
        },
        getCountViews : function(){
            return $('.'+this.windowClass).length;
        },
        reply : function(event){
            this.close(event);
        },
        noReply : function(event){
            this.close(event);
        }
   });         
});
