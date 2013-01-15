/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection
 */

$(function () {
    "use strict";

    window.SKPhoneView = SKWindowView.extend({
        title: "Телефон",
        events:_.defaults({
            'click .phone_get_contacts':'getContacts',
            'click .phone_get_history':'getHistory',
            'click .phone_get_menu':'getMenu'
        }, SKWindowView.prototype.events),
        renderContent: function (window_el) {
            window_el.html(_.template($('#Phone_Html').html(), _.defaults({windowID:this.windowID}, SKConfig)));
        },
        getContacts: function (event) {
            //$('#'+id+' .phone-screen')
            var contacts = new SKPhoneContactsCollection();
            contacts.fetch();
            var me = this;
            contacts.on('reset', function () {
                me.renderTPL('.phone-screen','#Phone_Contacts', {contacts:contacts});
            });
        },
        getHistory: function (event) {
            var id = $(event.toElement).attr('window_id');
            
            var history = SKApp.user.simulation.phone_history;
            history.fetch();
            var me = this;
            history.on('reset', function () {
                me.renderTPL('#'+id+' .phone-screen', '#Phone_History', {history:history, types:['in','out','miss']});
            });
        },
        getMenu: function(event){
            var id = $(event.toElement).attr('window_id');
            this.renderTPL('.phone-screen', '#Phone_Menu', {windowID:id});
        },
        getCountViews : function(){
            return $('.'+this.windowClass).length;
        }
    });
});
