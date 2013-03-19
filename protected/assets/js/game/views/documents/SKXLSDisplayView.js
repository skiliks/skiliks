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

            me.listenTo(SKApp.simulation.documents,'zoho-500', me.reloadDocumentViaZoho500());
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

            me.displayZohoIframe(doc, el);

            if (true === SKApp.simulation.documents.excelErrorHappened) {
                me.reloadDocumentViaZoho500();
            }
        },

        reloadDocumentViaZoho500: function() {
            var me = this;
            var doc = this.options.model_instance.get('document');

            console.log('reloadDocumentViaZoho500');

            me.message_window = new SKDialogView({
                'message': 'Excel выполнил недопустимую операцию. <br/> Необходимо закрыть и заново открыть документ.',
                'buttons': [
                    {
                        'value': 'Подтвердить',
                        'onclick': function () {
                            console.log('accept');

                            SKDocument._excel_cache = {};
                            console.log(SKApp.simulation.documents);
                            SKApp.simulation.documents.fetch();
                            console.log(SKApp.simulation.documents);

                            SKApp.simulation.documents.excelErrorHappened = false;

                            me.doWindowClose();

                            delete me.message_window;
                            console.log('accept - 2');
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