/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection
 */

(function () {
    "use strict";

    window.SKXLSDisplayView = SKWindowView.extend({
        title:'Мои документы',
        renderContent:function (el) {
            var me = this;
            var fn = this.options.model_instance.get('filename');
            var doc = SKApp.user.simulation.documents.where({name:fn})[0];
            el.html(_.template($('#document_xls_template').html(), {
                'filename': doc.get('excel_url')
            }));
        }
    });
})();