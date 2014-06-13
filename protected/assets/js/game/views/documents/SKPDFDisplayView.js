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
            /**
             * Переопределение поведения SKWindowView
             * @var Boolean
             */
            isDisplaySettingsButton:false,

            /**
             * Переопределение поведения SKWindowView
             * @var String
             */
            title:'Просмотр документа',

            /**
             * Переопределение поведения SKWindowView
             * @var String
             */
            addClass: 'document-window',

            /**
             * Переопределение поведения SKWindowView
             * @var Array.<Number>
             */
            dimensions: {},

            /**
             * Constructor
             */
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
             * Рекурсивно отрисовывает все страницы документа
             * @param ? (Object) pdf
             * @param Number page_num
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
             * Содержимое окна Word и PowerPoint выше чем надо - оно 100%.
             * Приходится его вписать в область допустимую для окна внутри игры.
             */
            fitHeight: function() {
                this.$el.find('.pdf-container').css(
                    'height',
                    ($('#canvas').height() - 105) + 'px'
                );
            },

            /**
             * Стандартный родительский метод
             * @param {jQuery} el
             */
            renderContent:function (el) {
                try {
                    var me = this;
                    el.html(
                        _.template(
                            document_pdf_template,
                            { pages:                   this.options.model_instance.get('document').get('pages'),
                              isDisplaySettingsButton: this.isDisplaySettingsButton,
                              documents_path:          'http://' + window.location.hostname + window.assetsUrl + '/img/documents/templates/'
                            }
                        )
                    );
                    me.fitHeight();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Изменение размеров окна екселя в игре, при изменении размеров окна браузера
             */
            onResize: function() {
                try {
                    var me = this;

                    window.SKWindowView.prototype.onResize.call(me);
                    me.fitHeight();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });

    return SKPDFDisplayView;
});