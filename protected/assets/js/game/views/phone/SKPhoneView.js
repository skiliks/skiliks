/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection
 */
var SKPhoneView;

define([
    "text!game/jst/phone/phone_contacts.jst",
    "text!game/jst/phone/phone_history.jst",
    "text!game/jst/phone/phone_menu.jst",
    "text!game/jst/phone/phone_themes.jst",
    "text!game/jst/phone/main_template.jst",

    "game/views/SKWindowView"
], function (
        phone_contacts,
        phone_history,
        phone_menu,
        phone_themes,
        main_template
    ) {
    "use strict";

    SKPhoneView = SKWindowView.extend({
        title: "Телефон",
        events:_.defaults({
            'click .phone_get_contacts': 'getContacts',
            'click .phone_get_history':  'getHistory',
            'click .phone_get_menu':     'getMenu',
            'click .phone_get_themes':   'getThemes',
            'click .phone_call':         'callToContact',
            'click .phone_call_back':    'callbackContact'
        }, SKWindowView.prototype.events),
        renderContent: function (window_el) {
            window_el.html(_.template(main_template, _.defaults(SKConfig)));
        },
        getContacts: function () {
            //$('#'+id+' .phone-screen')
            var contacts = new SKPhoneContactsCollection();
            contacts.fetch();
            var me = this;
            contacts.on('reset', function () {
                me.renderTPL('.phone-screen', phone_contacts, {contacts:contacts});
                me.$('.phone-screen').mCustomScrollbar();
            });
        },
        getHistory: function () {

            var history = SKApp.user.simulation.phone_history;
            
            history.readHistory();
            history.trigger('reset'); // to refresh counter
            
            var me = this;
            history.on('reset', function () {
                me.renderTPL('.phone-screen', phone_history, {history:history, types:['in','out','miss']});
            });
        },
        getMenu: function(){
            this.renderTPL('.phone-screen', phone_menu);
        },
        getCountViews : function(){
            return $('.'+this.windowClass).length;
        },
        getThemes: function(event){
            event.preventDefault();

            var el = $('#phoneCallThemesDiv');
            if(el.length == 0) {
                this.$el.append('<div id="phoneCallThemesDiv" class="mail-new-drop" style="position: absolute; z-index: 58; top: 50px; left: 2px; width: 354px; overflow: hidden; overflow-y: scroll;"></div>');
            }

            var contactId = $(event.currentTarget).attr('data-contact-id');
            var themes = new SKPhoneThemeCollection({characterId: contactId});
            themes.fetch();
            
            var me = this;
            themes.on('reset', function () {
                me.renderTPL('#phoneCallThemesDiv', phone_themes, {'themes':themes, 'contactId':contactId});
                me.undelegateEvents();
                me.delegateEvents();
            });
        },
        callToContact:function(event){
            var themeId = $(event.currentTarget).attr('data-theme-id');
            var contactId = $(event.currentTarget).attr('data-contact-id');
            this.options.model_instance.close();
            SKApp.server.api('phone/call', {'themeId':themeId, 'contactId':contactId, 'time':SKApp.user.simulation.getGameTime()}, function (data) {
                SKApp.user.simulation.parseNewEvents(data.events);
            });

        },
        callbackContact:function(e){
            var dialog_code = $(e.currentTarget).attr('data-dialog-code');
            this.options.model_instance.close();
            SKApp.server.api('phone/callback', {'dialog_code':dialog_code, 'time':SKApp.user.simulation.getGameTime()}, function (data) {
                if(data.data === 'ok'){
                    SKApp.user.simulation.getNewEvents();
                }else{
                    SKApp.user.simulation.mailClient.message_window = new SKDialogView({
                        'message':'Вы уже обсудили этот вопрос!',
                        'buttons':[
                            {
                                'value':'Окей',
                                'onclick':function () {
                                    delete SKApp.user.simulation.mailClient.message_window;
                                }
                            }
                        ]
                    });
                }

            });

        }
    });

    return SKPhoneView;
});
