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

        /**
         * Переопределение поведения SKWindowView
         * @var Boolean
         */
        isDisplaySettingsButton:false,

        /**
         * Переопределение поведения SKWindowView
         * @var String
         */
        title:'Мои документы',

        /**
         * Переопределение поведения SKWindowView
         * @var String
         */
        addClass: 'document-window document-excel',

        /**
         * У родительского объекта данный параметр тоже присутствует,
         * но если убрать dimensions из SKXLSDisplayView то maxWidth и maxHeight вычисляются неверно/
         * Странно.
         *
         * @var Array {
         *  height:    Number,
         *  width:     Number,
         *  maxHeight: Number,
         *  maxWidth:  Number
         * }
         */
        dimensions: {},

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        events: _.defaults({
            'click .sheet-tabs li': 'doSelectTab',
            'click .xls-container': 'doActivateRedirect'
        }, SKWindowView.prototype.events),

        /**
         * Если false - документ будет удалён из списка окон игры, SKApp.simulation.window_set.
         * @var Boolean
         */
        isRender: true,

        /**
         * @var Array.<SKSheetView>
         */
        sheets:[],

        /*
        * Constructor
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
         * @param jQUery el
         * @param SKWindowView windowObject
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
                me.$('#'+doc.getCssId()).find('.table-container').css('min-height', me.$('#'+doc.getCssId()).height()-26);

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

                // высота шапки окна екселя + высота закладок
                var excelNavigationTotalHeight =
                    this.$el.find('.header-inner').height()
                    + this.$el.find('.toolbar').height()
                    + this.$el.find('.sheet-tabs').height()
                    - 42; // это реально магическое число

                var newHeight = this.$el.height() - excelNavigationTotalHeight;

                if (activeSheetView.oldWidth == activeSheetView.$el.width() &&
                    activeSheetView.oldHeigth == newHeight ) {
                    // нам не надо реперисовывать скролы, если размеры окна не поменялись
                    // перерисовка занимает время - в это время не работают горячие клавиши копирования
                    return;
                }

                // /protected/assets/js/socialcalc/socialcalcspreadsheetcontrol.js:178
                activeSheetView.spreadsheet.InitializeSpreadsheetControl(
                    $(activeSheetView.el).attr('id'),
                    newHeight,
                    activeSheetView.$el.width(),
                    0
                );

                activeSheetView.spreadsheet.ExecuteCommand('recalc', '');
                activeSheetView.spreadsheet.ExecuteCommand('redisplay', '');

                activeSheetView.oldWidth = activeSheetView.$el.width();
                activeSheetView.oldHeigth = newHeight;

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
                me.$('.table-container').css('min-height', '');
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