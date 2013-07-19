/*global SKWindow, _, SKDocument,  SKConfig, SKWindowView, SKApp, SKPhoneContactsCollection, SKDialogView, define, console, $,
 SocialCalc */

define([
    "text!game/jst/document/document_xls_template.jst",
    "text!game/jst/document/budget.jst",
    "game/views/SKWindowView",
    "game/views/documents/spreadsheets/SKSheetView"
],function (
    document_xls_template,
    budget,
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
            'click .sheet-tabs li': 'doSelectTab'
        }, SKWindowView.prototype.events),

        isRender: true,

        sheets:[],

        /*
        * Constructor
        * @method initialize
        */
        initialize: function () {
            var me = this;
            var doc = me.options.model_instance.get('document');
            doc.get();
            this.title = doc.get('name') || 'Без названия';

            window.SKWindowView.prototype.initialize.call(this);
            return true;
        },


        /**
         * @method
         * @param el
         */
        renderContent:function (el) {
            var me = this;
            var doc = this.options.model_instance.get('document');
            var spreadsheet;
            el.html( _.template(document_xls_template, {
                sheets: doc.get('sheets')
            }) );

            SocialCalc.Constants.defaultImagePrefix = SKApp.get('assetsUrl') + '/img/excel/sc-';
            me.sheets = [];
            doc.get('sheets').each(function (sheet, i) {
                var sheetView = new SKSheetView({
                    'el': me.$('.table-container'),
                    'sheet': sheet
                });
                sheetView.render();
                //console.log(me, me.sheets, sheetView);
                me.sheets.push(sheetView);
                me.listenTo(sheet, 'activate', function() {
                    $('.sheet-tabs li').removeClass('active').filter('[data-sheet-name=' + sheet.get('name') + ']').addClass('active');
                });

                if (i === 0) {
                    sheet.activate();
                }
            });
        },

        doSelectTab: function doSelectTab (event) {
            var doc = this.options.model_instance.get('document');
            doc.get('sheets').where({'name': $(event.target).attr('data-sheet-name')})[0].activate();
        },

        onResize: function() {
            console.log(this.el);

            //this.renderContent(this.$('.sim-window-content'));
            window.SKWindowView.prototype.onResize.call(this);
            var me = this;
            console.log(this.sheets);
            $.each(this.sheets, function(index, sheet){
                //console.log(sheet.el);
                sheet.spreadsheet.InitializeSpreadsheetControl($(sheet.el).attr('id'), $(me.el).height() - $(me.el).find('.header-inner').height() - $(me.el).find('.sheet-tabs').height(), $(me.el).width(), 0);
                //sheet.spreadsheet.editor.LoadEditorSettings(sheet.options.sheet.get('content').substring(sheet.parts.edit.start, sheet.parts.edit.end));
                sheet.spreadsheet.ExecuteCommand('recalc', '');
                sheet.spreadsheet.ExecuteCommand('redisplay', '');

            });
            console.log(this.el);
            //doc.get('sheets').each(function (sheet, i) {
            //    sheet.recalcSheet();
            //});
        },
        remove: function () {
            this.sheets = [];
            window.SKWindowView.prototype.remove.apply(this);

        }

    });

    return SKXLSDisplayView;
});