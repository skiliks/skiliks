/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection
 */

(function () {
    "use strict";

    window.SKPhoneView = SKWindowView.extend({
        title: "Телефон",
        events:_.defaults({
            'click .phone_get_contacts':'getContacts',
            'click .phone_get_history':'getHistory',
            'click .phone_get_menu':'getMenu',
            'click .phone_get_themes':'getThemes',
            'click .phone_call':'callToContact'
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
        },
        getThemes: function(event){
            this.$el.append('<div id="phoneCallThemesDiv" class="mail-new-drop" style="position: absolute; z-index: 58; top: 50px; left: 14px; width: 330px;"></div>');
            var contactId = $(event.toElement).attr('data-contact-id');
            //var windowId = $(event.toElement).attr('window_id');
            var themes = new SKPhoneThemeCollection({'id':contactId});
            themes.fetch();
            var me = this;
            //console.log(themes);
            themes.on('reset', function () {
                me.renderTPL('#phoneCallThemesDiv', '#Phone_Themes', {'themes':themes, 'contactId':contactId});
                me.undelegateEvents();
                me.delegateEvents();
            });
        },
        callToContact:function(event){
            console.log("Run");
            /*var themeId = $(event.toElement).attr('data-theme-id');
            var contactId = $(event.toElement).attr('data-contact-id');
            SKApp.server.api('phone/getThemes', {'themeId':themeId, 'contactId':contactId}, function (data) {
                console.log(data);
                //options.success(data);
            });
            */
        }
    });
})();
