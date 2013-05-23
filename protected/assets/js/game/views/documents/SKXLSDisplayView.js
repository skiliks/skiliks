/*global SocialCalc, _
 */

define([
    "text!game/jst/document/document_xls_template.jst",
    "text!game/jst/document/budget.jst",
    "game/views/SKWindowView"
],function (
    document_xls_template,
    budget,
    SKWindowView
) {
    "use strict";

    /**
     * @class SKXLSDisplayView
     * @augments Backbone.View
     */
    var SKXLSDisplayView = SKWindowView.extend({

        title:'Мои документы',
        addClass: 'document-window',

        dimensions: {},

        isRender: true,

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
            el.html( _.template(document_xls_template, {}) );

            SocialCalc.Constants.defaultImagePrefix = SKApp.get('assetsUrl') + '/js/socialcalc/images/sc-';
            doc.getContent(function (response) {
                setTimeout(function () {
                    response.data.forEach(function (sheet) {
                        spreadsheet = new SocialCalc.SpreadsheetControl();
                        var parts = spreadsheet.DecodeSpreadsheetSave(sheet.content);
                        var root = $('<div></div>').attr('id',  _.uniqueId('tableeditor-'));
                        root.appendTo($('#tableeditor'));
                        spreadsheet.InitializeSpreadsheetControl(root.attr('id'), me.$('.xls-container').height() - 50, me.$('.xls-container').width(), 0);
                        spreadsheet.ParseSheetSave(sheet.content);
                        spreadsheet.ExecuteCommand('recalc', '');
                        spreadsheet.ExecuteCommand('redisplay', '');
                    });


                });
            });
            setInterval(function () {
                console.log(spreadsheet.CreateSheetSave());
            }, 60000);

        },

        /**
         * @method
         * @param el
         */
        doStartDrag: function (el) {
            // this.hideZohoIframe();
        },

    });

    return SKXLSDisplayView;
});