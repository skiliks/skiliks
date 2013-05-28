/*global SocialCalc, _*/
define([], function () {
    "use strict";
    var loadQueue = $({});
    var SKSheetView = Backbone.View.extend({
        /**
         *
         * @param {SKSheet} sheet
         */
        render: function () {
            var me = this;
            var sheet = this.options.sheet;
            loadQueue.queue('fx', function () {
                console.log('sheet init');
                var editorID = _.uniqueId('tableeditor-');
                var spreadsheet = me.spreadsheet = new SocialCalc.SpreadsheetControl();
                SocialCalc.Formula.AddSheetToCache(sheet.get('name'),sheet.get('content'));
                spreadsheet.editor.idPrefix = editorID + '-';
                spreadsheet.editor.StatusCallback.continue_queue = {
                    func: function(object, cmdtype) {
                        console.log(arguments);
                        if (!sheet.loaded && cmdtype === "doneposcalc") {
                            sheet.loaded = true;
                            console.log("status queue");
                            loadQueue.dequeue('fx');
                        }
                    }
                };
                var parts = spreadsheet.DecodeSpreadsheetSave(sheet.get('content'));
                var root = $('<div></div>')
                    .addClass('table-editor')
                    .attr('id', editorID);
                me.rootView = root;
                root.appendTo(me.$el);
                root.hide();
                spreadsheet.InitializeSpreadsheetControl(root.attr('id'), me.$el.height() - 50, me.$el.width(), 0);
                spreadsheet.ParseSheetSave(sheet.get('content'));
                me.spreadsheet.ExecuteCommand('recalc', '');
                me.spreadsheet.ExecuteCommand('redisplay', '');

            });
            me.listenTo(sheet, 'activate', function () {
                loadQueue.queue('fx', function () {
                    me.rootView.show();
                    me.spreadsheet.editor.SchedulePositionCalculations();
                    loadQueue.dequeue('fx');

                });
            });
            me.listenTo(sheet, 'deactivate', function () {
                loadQueue.queue('fx', function () {
                    me.rootView.hide();
                    loadQueue.dequeue('fx');
                });
            });




        }
    }, {});
    return SKSheetView;
});