/*global SKWindow, _, SKDocument,  SKConfig, SKWindowView, SKApp, SKPhoneContactsCollection, SKDialogView, define, console, $,
 SocialCalc */

define([
    "text!game/jst/document/document_xls_template.jst",
    "game/views/SKWindowView",
    "game/views/documents/spreadsheets/SKSheetView"
],function (
    document_xls_template,
    SKWindowView,
    SKSheetView
) {
    "use strict";

    /**
     * @class SKXLSDisplayView
     * @augments Backbone.View
     */
    var SKXLSDisplayView = SKWindowView.extend({

        isDisplaySettingsButton:false,

        title:'Мои документы',
        addClass: 'document-window document-excel',

        dimensions: {},
        events: _.defaults({
            'click .sheet-tabs li': 'doSelectTab',
            'click .xls-container': 'doActivateRedirect'
        }, SKWindowView.prototype.events),

        isRender: true,

        sheets:[],

        /*
        * Constructor
        * @method initialize
        */
        initialize: function () {
            try {
                var me = this;
                var doc = me.options.model_instance.get('document');
                doc.get();
                this.title = doc.get('name') || 'Без названия';

                window.SKWindowView.prototype.initialize.call(this);
                return true;
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
        renderContent:function (el, windowObject) {
            try {
                var me = this;
                me.windowObject = windowObject;
                var doc = this.options.model_instance.get('document');
                var spreadsheet;
                el.html( _.template(document_xls_template, {
                    css_id: doc.getCssId(),
                    sheets: doc.get('sheets')
                }) );

                SocialCalc.Constants.defaultImagePrefix = SKApp.get('assetsUrl') + '/img/excel/sc-';
                me.sheets = [];
                this.block();
                SKApp.simulation.useSCHotkeys = false;
                doc.get('sheets').each(function (sheet, i) {
                    var sheetView = new SKSheetView({
                        'el':     me.$('.table-container'),
                        'sheet':  sheet
                    });
                    sheetView.render();

                    me.sheets.push(sheetView);
                    me.listenTo(sheet, 'activate', function() {
                        $('#' + doc.getCssId() + ' .sheet-tabs li').removeClass('active').filter('[data-sheet-name=' + sheet.get('name') + ']').addClass('active');
                    });

                    if (i === 0) {
                        sheet.activate();
                    }
                });

                // зачем?
                me.$('.header-inner').click();

                clearInterval(SKApp.simulation.sc_interval_id);
                SKApp.simulation.sc_interval_id = setInterval(function(){
                    if(document.body.style.cursor !== "progress"){
                        me.unBlock();
                        SKApp.simulation.useSCHotkeys = true;
                        clearInterval(SKApp.simulation.sc_interval_id);
                        me.doHoverMenuIcon();
                    }
                    //me.$('.header-inner').click();
                }, 1000);
                setTimeout(function(){
                    if(null !== SKApp.simulation.sc_interval_id) {
                        me.unBlock();
                        SKApp.simulation.useSCHotkeys = true;
                        clearInterval(SKApp.simulation.sc_interval_id);
                        me.doHoverMenuIcon();
                    }
                }, 10000);

            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        doSelectTab: function doSelectTab (event) {
            try {
                var doc = this.options.model_instance.get('document');
                doc.get('sheets').where({'name': $(event.target).attr('data-sheet-name')})[0].activate();

                this.resizeActiveTab();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        resizeActiveTab: function(isResize) {
            try {
                var doc = this.options.model_instance.get('document');
                var me = this;

                var activeSheet = doc.get('sheets').where({
                    'name':  $('#' + doc.getCssId() + ' .sheet-tabs li.active').attr('data-sheet-name')
                })[0];

                var activeSheetView = undefined;

                $.each(this.sheets, function(index, sheet) {
                    if (sheet.options.sheet.get('name') == activeSheet.get('name')) {
                        activeSheetView = sheet;
                    }
                });

                if (activeSheetView.oldWidth == activeSheetView.$el.width() &&
                    activeSheetView.oldHeigth == activeSheetView.$el.height() ) {
                    // нам не надо реперисовывать скролы, если размеры окна не поменялись
                    // перерисовка занимает время - в это время не работают горячие клавиши копирования
                    return;
                }

                if ('undefined' == typeof isResize) {
                    isResize = false;
                }

                /**
                 * IE (10) не может перерисовать SC нормально SKILIKS-4803, SKILIKS-4804.
                 * Попытки сменить схему расчёта в activeSheetView.spreadsheet.InitializeSpreadsheetControl конкретно для IE
                 * не привели к результату.
                 * Задача заняла уже день, чтоисправить с js коде самого SC непонятно,
                 * поэтому решение -- заново вызвать renderWindow() только для IE,
                 * вместо перерисования активной вкладки.
                 */
                // Internet explorer {
                // если вкладка открывается не первый раз: ('undefined' != typeof activeSheetView.oldWidth или Heigth)
                // также надо обязательно перерисовывать окно при ресайзе в IE
                if ( isResize && $.browser['msie']
                    || ('undefined' != typeof activeSheetView.oldWidth
                        && 'undefined' != typeof activeSheetView.oldHeigth
                        && $.browser['msie'])) {

                    var id = activeSheetView.$el.attr('id');
                    var doc = me.options.model_instance.get('document');

                    this.renderWindow();

                    return;
                }
                // } Internet explorer

                // /protected/assets/js/socialcalc/socialcalcspreadsheetcontrol.js:178
                activeSheetView.spreadsheet.InitializeSpreadsheetControl(
                    $(activeSheetView.el).attr('id'),
                    activeSheetView.$el.height(),
                    activeSheetView.$el.width(),
                    0
                );

                activeSheetView.spreadsheet.ExecuteCommand('recalc', '');
                activeSheetView.spreadsheet.ExecuteCommand('redisplay', '');

                activeSheetView.oldWidth = activeSheetView.$el.width();
                activeSheetView.oldHeigth = activeSheetView.$el.height();

                // показать скрытую, для данного окна, строку ввода формул
                this.$('.menu_bar').show();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        onResize: function() {
            try {
                window.SKWindowView.prototype.onResize.call(this);
                var me = this;
                me.resizeActiveTab(true);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        remove: function () {
            try {
                this.sheets = [];
                window.SKWindowView.prototype.remove.apply(this);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        doActivateRedirect: function() {
            try {
                this.windowObject.doActivate();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        doHoverMenuIcon:function() {
            $('.button_menu li').hover(function(){
                $(this).find('a.grid-row').css('text-decoration', 'underline');
            }, function(){
                $(this).find('a.grid-row').css('text-decoration', 'none');
            });
        }

    });

    return SKXLSDisplayView;
});