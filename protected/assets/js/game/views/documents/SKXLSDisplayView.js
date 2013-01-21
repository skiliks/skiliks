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
                'top':el.parents('.sim-window')[0].offsetTop + el[0].offsetTop,
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
            var fn = this.options.model_instance.get('filename');
            var doc = SKApp.user.simulation.documents.where({name:fn})[0];
            this.hideZohoIframe();
        },
        doEndDrag: function (el) {
            var fn = this.options.model_instance.get('filename');
            var doc = SKApp.user.simulation.documents.where({name:fn})[0];
            this.displayZohoIframe(doc, el.find('.sim-window-content'));
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