/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection
 */

(function () {
    "use strict";

    window.SKXLSDisplayView = SKWindowView.extend({
        title:'Мои документы',
        renderContent:function (el) {
            var me = this;
            SKApp.server.api('excelDocument/get', {
                'fileName':decodeURIComponent(this.options.model_instance.get('filename'))
            }, function (data) {
                me.options.excel_url = data.excelDocumentUrl;
                el.html(_.template($('#document_xls_template').html(), {filename:me.options.excel_url}));
            });

        }
    });
})();