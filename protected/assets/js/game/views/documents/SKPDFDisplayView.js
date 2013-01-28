/*global SKPDFDisplayView:true, SKWindow, _, SKWindowView:false, SKConfig, SKApp, SKPhoneContactsCollection
 */

/**
 * @class SKPDFDisplayView
 * @augments SKWindowView
 */
var SKPDFDisplayView;

(function () {
    "use strict";

    SKPDFDisplayView = SKWindowView.extend(
        /** @lends SKPDFDisplayView.prototype */
        {
            /**
             * @property {string} title
             */
            title:'Мои документы',
            renderContent:function (el) {
                el.html(
                    _.template(
                        $('#document_pdf_template').html(),
                        {
                            filename: this.options.model_instance.get('document').get('srcFile')
                        }
                    )
                );
            }
        });
})();