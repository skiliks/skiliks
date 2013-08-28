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
                //debugger;
                console.log('before render');
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
                console.log('after render');
                me.$('.header-inner').click();
                clearInterval(SKApp.simulation.sc_interval_id);
                SKApp.simulation.sc_interval_id = setInterval(function(){
                    if(document.body.style.cursor !== "progress"){
                        me.unBlock();
                        clearInterval(SKApp.simulation.sc_interval_id);
                    }
                    //me.$('.header-inner').click();
                }, 1000);
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

        resizeActiveTab: function() {
            try {
                var doc = this.options.model_instance.get('document');

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
                    activeSheetView.oldHeidth == activeSheetView.$el.height()) {
                    // нам не надо реперисовывать скролы, если размеры окан не поменялись
                    // перерисовка занимает время - в это время не работают горячие клавиши копирования
                    return;
                }

                activeSheetView.spreadsheet.InitializeSpreadsheetControl($(activeSheetView.el).attr('id'), activeSheetView.$el.height(), activeSheetView.$el.width(), 0);
                activeSheetView.spreadsheet.ExecuteCommand('recalc', '');
                activeSheetView.spreadsheet.ExecuteCommand('redisplay', '');

                activeSheetView.oldWidth = activeSheetView.$el.width();
                activeSheetView.oldHeidth = activeSheetView.$el.height();
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
                me.resizeActiveTab();
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
        }

    });

    return SKXLSDisplayView;
});