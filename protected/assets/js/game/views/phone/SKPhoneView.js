/*global SKWindow
 */

$(function () {
    "use strict";

    window.SKPhoneView = window.SKWindowView.extend({
        initialize:function () {
            if(this.countMaxView > this.getCountViews()){
                this.open();
            } else {
                this.close();
            }
        },
        el:null,
        countMaxView: 1,
        windowClass: "phoneMainDiv",
        windowID: '',
        SKWindow:null,
        events:{
            'click .btn-cl':'close',
            'click .phone_get_contacts':'getContacts',
            'click .phone_get_history':'getHistory',
            'click .phone_get_menu':'getMenu'
        },
        open: function () {
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
            this.renderTPL('#'+this.windowID, '#Phone_Html', {windowID:this.windowID});
            this.$el = $('#'+this.windowID);
            this.SKWindow = new SKWindow({name:'phone', subname:'phoneMain'});
            this.SKWindow.open();
        },
        close:function (event) {            
            if(event !== undefined){
                var id = $(event.toElement).attr('window_id');
                $('#'+id).remove();
            }else{
                $('.'+this.windowClass).remove();
            }
            this.SKWindow.close();
        },
        getContacts: function (event) {
            console.log(event.toElement);
            var id = $(event.toElement).attr('window_id');
            
            //$('#'+id+' .phone-screen')
            var contacts = new SKPhoneContactsCollection();
            contacts.fetch();
            console.log('#'+id+' .phone-screen');
            var me = this;
            contacts.on('reset', function () {
                me.renderTPL('#'+id+' .phone-screen', '#Phone_Contacts', {contacts:contacts});
                me.doScroll(id);
            });
        },
        getHistory: function (event) {
            console.log(event.toElement);
            var id = $(event.toElement).attr('window_id');
            
            var history = SKApp.user.simulation.phone_history;
            history.fetch();
            console.log('#'+id+' .phone-screen');
            var me = this;
            history.on('reset', function () {
                me.renderTPL('#'+id+' .phone-screen', '#Phone_History', {history:history, types:['in','out','miss']});
                me.doScroll(id);
            })
        },
        getMenu: function(event){
            var id = $(event.toElement).attr('window_id');
            this.renderTPL('#'+id+' .phone-screen', '#Phone_Menu', {windowID:id});
        },
        getCountViews : function(){
            return $('.'+this.windowClass).length;
        }, 
        doScroll: function(id){
            $('#'+id+' .phone-screen').mCustomScrollbar({'advanced':{'updateOnContentResize':true}});
        }
    });
});
