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
            renderPage:function (pdf, page_num) {
                var me = this;
                pdf.getPage(page_num).then(function (page) {

                    var viewport = page.getViewport(1);
                    var pageDisplayWidth = viewport.width;
                    var pageDisplayHeight = viewport.height;

                    var pageDivHolder = document.createElement('div');
                    pageDivHolder.className = 'pdfpage';
                    pageDivHolder.style.width = pageDisplayWidth + 'px';
                    pageDivHolder.style.height = pageDisplayHeight + 'px';
                    me.$('.pdf-container').append(pageDivHolder);

                    // Prepare canvas using PDF page dimensions
                    var canvas = document.createElement('canvas');
                    var context = canvas.getContext('2d');
                    canvas.width = pageDisplayWidth;
                    canvas.height = pageDisplayHeight;
                    pageDivHolder.appendChild(canvas);


                    // Render PDF page into canvas context
                    var renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    page.render(renderContext).then(function () {
                        if (page_num > pdf.numPages) {
                            return;
                        }
                        me.renderPage(pdf, page_num + 1);
                    });

                });
            }, renderContent:function (el) {
            var me = this;
                PDFJS.disableWorker = true;
                PDFJS.getDocument('/documents/templates/' + this.options.model_instance.get('document').get('srcFile'))
                    .then(function (pdf) {
                        var page_num = 1;
                        me.renderPage(pdf, page_num);
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