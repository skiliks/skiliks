/*global SKPDFDisplayView:true, SKWindow, _, SKWindowView:false, SKConfig, SKApp, SKPhoneContactsCollection,PDFJS
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
                var me = this;
                PDFJS.disableWorker = true;
                PDFJS.getDocument('/documents/templates/' + this.options.model_instance.get('document').get('srcFile'))
                    .then(function (pdf) {
                        pdf.getPage(1).then(function (page) {
                            var viewport = page.getViewport(1);
                            var canvas = me.$('canvas')[0];
                            var context = canvas.getContext('2d');
                            canvas.height = viewport.height;
                            canvas.width = viewport.width;
                            page.render({canvasContext: context, viewport: viewport});
                        });

                    });
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