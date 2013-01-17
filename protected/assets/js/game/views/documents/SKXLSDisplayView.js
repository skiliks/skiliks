/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection
 */

(function () {
    "use strict";

    window.SKXLSDisplayView = SKWindowView.extend({
        title:'Мои документы',
        render:function () {
            var me = this;
            SKApp.server.api('excelDocument/get', {
                'fileName':decodeURIComponent(this.options.model_instance.get('filename'))
            }, function (data) {
                me.options.excel_url = data.excelDocumentUrl;
                SKWindowView.prototype.render.call(me);
            });
        },
        renderContent:function (el) {
            el.html(_.template($('#document_xls_template').html(), {filename:this.options.excel_url}));
        }
    });
})();