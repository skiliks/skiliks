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

    SKXLSDisplayView = SKWindowView.extend({

        title:'Мои документы',

        displayZohoIframe:function (doc, el) {
            var me = this;
            $('#excel-preload-' + doc.id).show().css({
                'zIndex':parseInt(el.parents('.sim-window').css('zIndex'),10) + 1,
                'width':el.width() - 6,
                'height':el.height(),
                'left':me.$el[0].offsetLeft,
                'top':el.parents('.sim-window')[0].offsetTop + el[0].offsetTop,
                'position':'absolute'
            });
        },

        renderContent:function (el) {
            var me = this;
            var doc = this.options.model_instance.get('document');

            el.html( _.template(document_xls_template, {}) );

            me.listenTo(this.options.model_instance, 'change:zindex', function () {
                me.displayZohoIframe(doc, el);
            });
            setTimeout(function() {
                me.displayZohoIframe(doc, el);
            }, 0);

        },

        doStartDrag: function (el) {
            this.hideZohoIframe();
        },

        doEndDrag: function (el) {
            var doc = this.options.model_instance.get('document');
            this.displayZohoIframe(doc, el.find('.sim-window-content'));
        },

        hideZohoIframe:function () {
            var doc = this.options.model_instance.get('document');
            $('#excel-preload-' + doc.id).css({'left':'-1000px','position':'absolute'});
        },

        remove:function () {
            var me = this;
            this.hideZohoIframe();
            SKWindowView.prototype.remove.call(this);
        }
    });

    return SKXLSDisplayView;
});