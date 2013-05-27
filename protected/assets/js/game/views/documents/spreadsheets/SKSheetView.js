/*global SocialCalc, _*/
define([], function () {
    "use strict";
    var SKSheetView = Backbone.View.extend({
        /**
         *
         * @param {SKSheet} sheet
         */
        render: function () {
            var me = this;
            var sheet = this.options.sheet;
            var spreadsheet = new SocialCalc.SpreadsheetControl();
            var parts = spreadsheet.DecodeSpreadsheetSave(sheet.get('content'));
            var root = $('<div></div>')
                .addClass('table-editor')
                .attr('id', _.uniqueId('tableeditor-'));
            this.rootView = root;
            root.appendTo(this.$el);
            root.hide();
            sheet.on('activate', function () {
                root.show();
            });
            sheet.on('dectivate', function () {
                root.hide();
            });
            spreadsheet.InitializeSpreadsheetControl(root.attr('id'), this.$el.height() - 50, this.$el.width(), 0);
            spreadsheet.ParseSheetSave(sheet.get('content'));
            spreadsheet.ExecuteCommand('recalc', '');
            spreadsheet.ExecuteCommand('redisplay', '');

        }
    }, {});
    return SKSheetView;
});