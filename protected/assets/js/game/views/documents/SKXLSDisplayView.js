/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection
 */

var SKXLSDisplayView;
define([
    "text!game/jst/document/document_xls_template.jst",
    "game/views/SKWindowView"
],function (
    document_xls_template
) {
    "use strict";

    /**
     * @class SKXLSDisplayView
     * @augments Backbone.View
     */
    SKXLSDisplayView = SKWindowView.extend({

        title:'Мои документы',

        dimensions: {},

        isRender: true,

        /*
        * Constructor
        * @method initialize
        */
        initialize: function () {
            var me = this;
            /*var doc = this.options.model_instance.get('document');

            if(undefined == typeof doc.get('excel_url') || 0 == $(doc.combineIframeId()).length) {
                me.message_window = new SKDialogView({
                    'message': 'Excel-документ ещё не загружен. <br/> Попробуйте открыть этот документ через 10 секунд. <br/> Всё должно быть ОК!',
                    'buttons': [
                        {
                            'value': 'Продолжить работу',
                            'onclick': function () {
                                delete me.message_window;
                            }
                        }
                    ]
                });
                me.isRender = false;
            } else {
                me.isRender = true;
            }*/

            this.zoho500callback = function(event){
                me.handlePostMessage(event);
            }

            if (window.addEventListener){
                window.addEventListener("message", this.zoho500callback, false);
            } else {
                window.attachEvent("onmessage", this.zoho500callback);
            }

            window.SKWindowView.prototype.initialize.call(this);

            return true;
        },

        /**
         * @method handlePostMessage
         * @param postMessage event
         * @return void
         */
        handlePostMessage: function(event) {

            console.log('event: ', event);

            var me = this;
            var doc = me.options.model_instance.get('document');

            if (SKApp.simulation.documents.zoho_500.indexOf(doc.get('id')) < 0) {
                SKApp.simulation.documents.zoho_500.push(doc.get('id'));
            } else {
                return;
            }

            if (event.data.type == "Zoho_500") {
                 me.message_window = new SKDialogView({
                    'message': 'Excel выполнил недопустимую операцию. <br/> Необходимо закрыть и заново открыть документ<br/> Будет загружена последняя автосохранённая копия.',
                    'buttons': [
                    {
                    'value': 'Перезагрузить',
                    'onclick': function () {
                        SKApp.simulation.afterZohoCrash = true;
                        delete SKDocument._excel_cache[doc.get('id')];
                        SKApp.simulation.documents.remove(doc);
                        SKApp.simulation.documents.fetch();
                        t
                        me.doWindowClose();
                        $(doc.combineIframeId()).remove();
                        me.remove();

                        // clean array of not handled zoho 500 {
                        var i = SKApp.simulation.documents.zoho_500.indexOf(doc.get('id'));
                        delete SKApp.simulation.documents.zoho_500[i];


                        delete me.message_window;
                 }
                }
               ]
            });




            }
        },

        /**
         * @method
         * @param doc
         * @param el
         */
        displayZohoIframe:function (doc, el) {
            if (false == this.isRender) {
                return;
            }

            var me = this;
            $(doc.combineIframeId()).show().css({
                'background-color': '#fff',
                'zIndex':   parseInt(el.parents('.sim-window').css('zIndex'),10) + 1,
                'width':    el.width() - 4,
                'height':   this.$('.xls-container').parent().parent().parent().height() - this.$('.xls-container').parent().parent().find('header').height(), //el.height(),
                'left':     me.$el[0].offsetLeft,
                'top':      el.parents('.sim-window')[0].offsetTop + el[0].offsetTop,
                'position': 'absolute'
            });
        },

        /**
         * @method
         * @param el
         */
        renderContent:function (el) {
            if (false == this.isRender) {
                return;
            }

            var me = this;
            var doc = this.options.model_instance.get('document');

            el.html( _.template(document_xls_template, {}) );

            me.listenTo(this.options.model_instance, 'change:zindex', function () {
                me.displayZohoIframe(doc, el);
            });

            // run me.displayZohoIframe after code done
            setTimeout(function() {
                me.displayZohoIframe(doc, el);
            }, 0);
        },

        /**
         * @method
         * @param el
         */
        doStartDrag: function (el) {
            // this.hideZohoIframe();
        },

        /**
         * @method
         * @param el
         */
        doEndDrag: function (el) {
            var doc = this.options.model_instance.get('document');
            this.displayZohoIframe(doc, el.find('.sim-window-content'));
        },

        /**
         * @method
         */
        hideZohoIframe:function () {
            var doc = this.options.model_instance.get('document');
            $(doc.combineIframeId()).css({'left':'-4000px','position':'absolute'});
        },

        /**
         * @method
         */
        remove:function () {
            var me = this;
            this.hideZohoIframe();

            var me = this;
            if (window.removeEventListener){
                window.removeEventListener("message", me.zoho500callback,false);
            } else {
                window.detachEvent("onmessage", me.zoho500callback);
            }

            SKWindowView.prototype.remove.call(this);
        }
    });

    return SKXLSDisplayView;
});