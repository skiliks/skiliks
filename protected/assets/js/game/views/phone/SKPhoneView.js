/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection, $, SKDialogView, define
 */
var SKPhoneView;

define([
    "game/collections/SKPhoneThemeCollection",

    "text!game/jst/phone/phone_contacts.jst",
    "text!game/jst/phone/phone_history.jst",
    "text!game/jst/phone/phone_menu.jst",
    "text!game/jst/phone/phone_themes.jst",
    "text!game/jst/phone/main_template.jst",

    "game/views/SKWindowView"
], function (
        SKPhoneThemeCollection,

        phone_contacts,
        phone_history,
        phone_menu,
        phone_themes,
        main_template
    ) {
    "use strict";
    /**
     * @class SKPhoneView
     * @augments Backbone.View
     */
    SKPhoneView = SKWindowView.extend({

        isDisplaySettingsButton:true,

        title: "Телефон",
        windowName:'phone',
        addClass: 'phone-window',
        addId: 'phone-window',
        events:_.defaults({
            'click .phone_get_contacts': 'getContacts',
            'click .phone_get_history':  'getHistory',
            'click .phone_get_menu':     'getMenu',
            'click .phone_get_themes':   'getThemes',
            'click .phone_call':         'callToContact',
            'click .phone_call_back':    'callbackContact',
            'click .themes_close':       'closeThemes'
        }, SKWindowView.prototype.events),

        dimensions: {
            width: 371,
            height: 560
        },

        /**
         * @method
         * @param window_el
         */
        renderContent: function (window_el) {
            try {
                window_el.html(_.template(main_template, SKApp.attributes, {
                    isDisplaySettingsButton:this.isDisplaySettingsButton,
                    windowName:this.windowName
                }));
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        getContacts: function () {
            //$('#'+id+' .phone-screen')
            try {
                this.renderTPL('.phone-screen', phone_contacts, {
                    'contacts': SKApp.simulation.characters.filter(function (character) {
                        /** @type {SKCharacter} character */
                        return character.get('code') !== "1" && character.get('phone') && (character.get('fio') || character.get('title'));
                    })
                });
                this.$('.phone-list-contacts').mCustomScrollbar();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        getHistory: function () {
            try {
                var me = this,
                    history = SKApp.simulation.phone_history;

                SKApp.server.api('phone/markMissedCallsDisplayed', {}, function(){});

                history.readHistory();
                history.trigger('reset'); // to refresh counter

                me.renderTPL('.phone-screen', phone_history, {history:history, types:['in','out','miss']});
                setTimeout(function(){
                    me.$('.phone-list-call-history').mCustomScrollbar();
                }, 0);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        getMenu: function(){
            try {
                this.renderTPL('.phone-screen', phone_menu);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @returns {Number|jQuery}
         */
        getCountViews : function() {
            try {
                return $('.'+this.windowClass).length;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param event
         */
        getThemes: function(event) {
            try {
                event.preventDefault();
                var el = $('#phoneCallThemesDiv');
                if(el.length === 0) {
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param event
         */
        callToContact:function(event) {
            try {
                var themeId = $(event.currentTarget).attr('data-theme-id');
                var contactId = $(event.currentTarget).attr('data-contact-id');
                this.options.model_instance.close();
                SKApp.simulation.events.lockEvents('phone/call');
                SKApp.server.api('phone/call', {'themeId':themeId, 'contactId':contactId}, function (data) {
                    SKApp.simulation.getNewEvents();
                    if(data.params !== 'already_call'){
                        SKApp.simulation.parseNewEvents(data.events, 'phone/call');
                    }else{
                        SKApp.simulation.mailClient.message_window = new SKDialogView({
                            'message':'Вы уже обсудили этот вопрос!',
                            'buttons':[
                                {
                                    'value':'Ок',
                                    'onclick':function () {
                                        delete SKApp.simulation.mailClient.message_window;
                                        SKApp.simulation.getNewEvents();
                                    }
                                }
                            ]
                        });
                    }
                    SKApp.simulation.events.unlockEvents();
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param e
         */
        callbackContact:function(e) {
            try {
                var dialog_code = $(e.currentTarget).attr('data-dialog-code');
                this.options.model_instance.close();
                SKApp.server.api('phone/callback', {'dialog_code':dialog_code}, function (data) {
                    if(data.data === 'ok'){
                        SKApp.simulation.getNewEvents();
                    }else{
                        SKApp.simulation.mailClient.message_window = new SKDialogView({
                            'message':'Вы уже обсудили этот вопрос!',
                            'buttons':[
                                {
                                    'value':'Ок',
                                    'onclick':function () {
                                        delete SKApp.simulation.mailClient.message_window;
                                    }
                                }
                            ]
                        });
                    }

                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        closeThemes: function() {
            this.$el.find('#phoneCallThemesDiv').remove();
        }
    });

    return SKPhoneView;
});
