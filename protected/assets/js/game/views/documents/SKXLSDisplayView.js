/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection
 */

(function () {
    "use strict";

    window.SKXLSDisplayView = SKWindowView.extend({
        title:'Мои документы',
        displayZohoIframe:function (doc, el) {
            var me = this;
            $('#excel-preload-' + doc.id).show().css({
                'zIndex':el.parents('.sim-window').css('zIndex') + 1,
                'width':el.width() - 6,
                'height':el.height(),
                'left':me.$el[0].offsetLeft,
                'top':me.$el[0].offsetTop + el[0].offsetTop,
                'position':'absolute'
            });
        }, renderContent:function (el) {
            var me = this;
            var fn = this.options.model_instance.get('filename');
            var doc = SKApp.user.simulation.documents.where({name:fn})[0];
            el.html(_.template($('#document_xls_template').html(), {
            }));
            setTimeout(function() {
                me.displayZohoIframe(doc, el);
            }, 0);

        },
        doStartDrag: function (el) {
            this.hideZohoIframe(doc, el);
        },
        doEndDrag: function (el) {
            this.hideZohoIframe(doc, el);
        },
        hideZohoIframe:function () {
            var fn = this.options.model_instance.get('filename');
            var doc = SKApp.user.simulation.documents.where({name:fn})[0];
            $('#excel-preload-' + doc.id).hide();
        }, remove:function () {
            var me = this;
            this.hideZohoIframe();
            SKWindowView.prototype.remove.call(this);
        }
    });
})();