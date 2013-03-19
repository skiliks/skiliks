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

        /*
        * Constructor
        * @method initialize
        */
        initialize: function () {
            window.SKWindowView.prototype.initialize.call(this);

            var me = this;
            if (window.addEventListener){
                window.addEventListener("message", function(){ me.handlePostMessage },false);
            } else {
                window.attachEvent("onmessage", function(){ me.handlePostMessage });
            }
        },

        /**
         * @method handlePostMessage
         * @param postMessage event
         * @return void
         */
        handlePostMessage: function(event) {
            var me = this;
            console.log('handlePostMessage');
            if (undefined != typeof event && event.origin !== "*") {
                console.log('zoho-500');
                var doc = me.options.model_instance.get('document');

                //doc.isHasZoho500 = true;

                me.message_window = new SKDialogView({
                    'message': 'Excel выполнил недопустимую операцию. <br/> Необходимо закрыть и заново открыть документ.',
                    'buttons': [
                        {
                            'value': 'Подтвердить',
                            'onclick': function () {
                                console.log('accept', me);

                                //doc.isHasZoho500 = false;



                                console.log('SKDocument._excel_cache 1: ', SKDocument._excel_cache);
                                console.log(SKApp.simulation.documents);

                                //SKDocument._excel_cache = {};

                                SKDocument._excel_cache[doc.get('id')] = {};
                                SKApp.simulation.documents.fetch();

                                console.log(SKApp.simulation.documents);
                                console.log('SKDocument._excel_cache 2: ', SKDocument._excel_cache);

                                //me.doWindowClose();

                                delete me.message_window;
                            }
                        },
                        {
                            'value': 'Отмена',
                            'onclick': function () {
                                console.log('decline');
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
            var me = this;
            $('#excel-preload-' + doc.id).show().css({
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
            $('#excel-preload-' + doc.id).css({'left':'-4000px','position':'absolute'});
        },

        /**
         * @method
         */
        remove:function () {
            var me = this;
            this.hideZohoIframe();
            SKWindowView.prototype.remove.call(this);
        }
    });

    return SKXLSDisplayView;
});