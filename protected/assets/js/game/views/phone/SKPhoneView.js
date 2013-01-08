/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {
    "use strict";

    window.SKPhoneView = window.SKWindowView.extend({
        initialize:function (){
            this.open();
        },
        el:null,
        windowClass: "phoneMainDiv",
        windowID: '',
        events:{
            'click .btn-cl':'close',
            'click .phone_get_contacts':'getContacts',
            'click .phone_get_history':'getHistory'
        },
        open: function (){
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
        },
        render:function () {
            
        },
        close:function (event) {
            var id = $(event.toElement).attr('window_id');
            $('#'+id).remove();
        },
        getContacts: function (event) {
            console.log(event.toElement);
            var id = $(event.toElement).attr('window_id');
            
            //$('#'+id+' .phone-screen')
            var contacts = new SKPhoneContactsCollection();
            contacts.fetch();
            console.log('#'+id+' .phone-screen');
            var me = this;
            contacts.on('reset', function () {me.renderTPL('#'+id+' .phone-screen', '#Phone_Contacts', {contacts:contacts});})
            //console.log(contacts.models);
        },
        getHistory:function (event) {
            console.log(event.toElement);
            var id = $(event.toElement).attr('window_id');
            
            var history = new SKPhoneHistoryCollection();
            history.fetch();
            console.log('#'+id+' .phone-screen');
            var me = this;
            history.on('reset', function () {me.renderTPL('#'+id+' .phone-screen', '#Phone_History', {history:history, types:['in','out','miss']});})
        }
    });
});
