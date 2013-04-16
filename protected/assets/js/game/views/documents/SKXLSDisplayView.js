/*global SKWindow, _, SKDocument,  SKConfig, SKWindowView, SKApp, SKPhoneContactsCollection, SKDialogView, define, console, $
 */

define([
    "text!game/jst/document/document_xls_template.jst",
    "game/views/SKWindowView",
],function (
    document_xls_template
) {
    "use strict";

    /**
     * @class SKXLSDisplayView
     * @augments Backbone.View
     */
    var SKXLSDisplayView = SKWindowView.extend({

        title:'Мои документы',

        dimensions: {},

        isRender: true,

        /*
        * Constructor
        * @method initialize
        */
        initialize: function () {
            var me = this;
            var doc = me.options.model_instance.get('document');

            console.log('SKApp.simulation.documents.zoho_500 = ', SKApp.simulation.documents.zoho_500);
            console.log('index = ', SKApp.simulation.documents.zoho_500.indexOf(doc.get('excel_url')));
            console.log('Id = ', doc.get('excel_url'));

            $.each(SKApp.simulation.documents.zoho_500, function(value) {
                console.log('zoho_500 URL length = ', value.length, value);
            });
            console.log('doc excel_url = ', doc.get('excel_url'));

            if (-1 < SKApp.simulation.documents.zoho_500.indexOf(doc.get('excel_url'))) {
                SKApp.simulation.documents.zoho_500[SKApp.simulation.documents.zoho_500.indexOf(doc.get('excel_url'))] = null;

                console.log('it is Zoho500 doc');
                console.log('SKApp.simulation.documents.zoho_500 (2) = ', SKApp.simulation.documents.zoho_500);

                me.message_window = new SKDialogView({
                    'message': 'Excel выполнил недопустимую операцию. <br/> Необходимо закрыть и заново открыть документ<br/> Будет загружена последняя автосохранённая копия.',
                    'buttons': [
                        {
                            'value': 'Перезагрузить',
                            'onclick': function () {
                                console.log('Перезагрузить nclick');
                                SKApp.simulation.afterZohoCrash = true;
                                delete SKDocument._excel_cache[doc.get('id')];
                                SKApp.simulation.documents.remove(doc);
                                SKApp.simulation.documents.fetch();

                                // clean array of not handled zoho 500 {
                                var i = SKApp.simulation.documents.zoho_500.indexOf(doc.get('id'));
                                delete SKApp.simulation.documents.zoho_500[i];


                                delete me.message_window;
                            }
                        }]
                });
            }

            console.log('Add listener in view.');
            if (window.addEventListener){
                window.addEventListener("message", _.bind(me.handlePostMessage, me), false);
            } else {
                window.attachEvent("onmessage", _.bind(me.handlePostMessage, me));
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
            console.log('handlePostMessage in view.');
            var me = this;
            // var doc = me.options.model_instance.get('document');
            var doc = null;

            $.each(SKDocument._excel_cache, function(id, url){
                url = url.replace('\r', '');
                console.log(url.replace('\r', ''), event.data.url.replace('\r', ''));
                console.log(url.replace('\r', '').lendth, event.data.url.replace('\r', '').lendth);
                console.log(url.replace('\r', '') === event.data.url.replace('\r', ''));
                console.log('--------------------------------------------------------------');
                if(url.replace('\r', '') === event.data.url.replace('\r', '')){
                    doc = SKApp.simulation.documents.where({id:id.toString()});
                }
            });

            if (null === doc) {
                return;
            }

            if (event.data.type === "Zoho_500") {
                me.message_window = new SKDialogView({
                    'message': 'Excel выполнил недопустимую операцию. <br/> Необходимо закрыть и заново открыть документ<br/> Будет загружена последняя автосохранённая копия.',
                    'modal': true,
                    'buttons': [
                        {
                            'value': 'Перезагрузить',
                            'onclick': function () {
                                SKApp.simulation.afterZohoCrash = true;
                                var doc = me.options.model_instance.get('document');
                                delete SKDocument._excel_cache[doc.get('id')];
                                SKApp.simulation.documents.remove(doc);
                                SKApp.simulation.documents.fetch();

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
            if (false === this.isRender) {
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
            if (false === this.isRender) {
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