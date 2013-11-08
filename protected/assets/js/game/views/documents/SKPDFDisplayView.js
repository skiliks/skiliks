/*global SKPDFDisplayView:true, SKWindow, _, SKWindowView:false, SKConfig, SKApp, SKPhoneContactsCollection,PDFJS
 */

/**
 * @class SKPDFDisplayView
 * @augments SKWindowView
 */
var SKPDFDisplayView;

define([
    "text!game/jst/document/document_pdf_template.jst",
    "game/views/SKWindowView"
],
    function (
        document_pdf_template,
        SKWindowView
    ) {
    "use strict";

    SKPDFDisplayView = SKWindowView.extend(
        /** @lends SKPDFDisplayView.prototype */
        {
            isDisplaySettingsButton:false,
            /**
             * @property {string} title
             */
            title:'Просмотр документа',
            addClass: 'document-window',

            dimensions: {
                width: 851,
                height: 648
            },

            initialize: function () {
                try {
                    SKWindowView.prototype.initialize.call(this);
                    this.title = this.options.model_instance.get('document').get('name') || 'Без названия';
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param pdf
             * @param page_num
             */
            renderPage:function (pdf, page_num) {
                try {
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
                            if (page_num >= pdf.numPages) {
                                return;
                            }
                            me.renderPage(pdf, page_num + 1);
                        });
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param el
             */
            renderContent:function (el) {
                try {
                    var me = this;
                    PDFJS.disableWorker = true;
                    PDFJS.getDocument('/documents/templates/' + this.options.model_instance.get('document').get('srcFile'))
                        .then(function (pdf) {
                            var page_num = 1;
                            me.renderPage(pdf, page_num);
                        });
                    el.html(
                        _.template(
                            document_pdf_template,
                            { filename: this.options.model_instance.get('document').get('srcFile'), isDisplaySettingsButton:this.isDisplaySettingsButton }
                        )
                    );
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });

    return SKPDFDisplayView;
});