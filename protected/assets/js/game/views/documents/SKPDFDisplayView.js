/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection
 */

(function () {
    "use strict";

    window.SKPDFDisplayView = SKWindowView.extend({
        title:'Мои документы',
        renderContent:function (el) {
            el.html(_.template($('#document_pdf_template').html(), {filename:this.options.model_instance.get('filename')}));
        }
    });
})();