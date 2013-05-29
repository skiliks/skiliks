/*global SocialCalc, _*/
define([], function () {
    "use strict";
    var loadQueue = $({});
    var sc = new SocialCalc.SpreadsheetControl();

    var SKSheetView = Backbone.View.extend({
        initialize: function () {
            var sheet = this.options.sheet;
            var parts = sc.DecodeSpreadsheetSave(sheet.get('content'));
            if (parts && parts.sheet) {
                var sheet_data = sheet.get('content').substring(parts.sheet.start, parts.sheet.end);
                SocialCalc.Formula.AddSheetToCache(sheet.get('name'), sheet_data);
            }
        },
        /**
         *
         * @param {SKSheet} sheet
         */
        render: function () {
            var me = this;
            var sheet = this.options.sheet;

            loadQueue.queue('fx', function () {
                console.log('sheet init');
                me.spreadsheet = new SocialCalc.SpreadsheetControl();
                var editorID = _.uniqueId('tableeditor-');
                var spreadsheet = me.spreadsheet;
                spreadsheet.editor.idPrefix = editorID + '-';
                spreadsheet.editor.StatusCallback.continue_queue = {
                    func: function(object, cmdtype) {
                        if (!sheet.loaded && cmdtype === "doneposcalc") {
                            sheet.loaded = true;
                            loadQueue.dequeue('fx');
                        }
                    }
                };
                var root = $('<div></div>')
                    .addClass('table-editor')
                    .attr('id', editorID);
                me.rootView = root;
                root.appendTo(me.$el);
                root.hide();

                var parts = me.spreadsheet.DecodeSpreadsheetSave(sheet.get('content'));
                if (parts && parts.sheet) {
                    var sheet_data = sheet.get('content').substring(parts.sheet.start, parts.sheet.end);
                    me.spreadsheet.ParseSheetSave(sheet_data);
                }
                if (parts && parts.edit) {
                    console.log(sheet.get('content').substring(parts.edit.start, parts.edit.end));
                    me.spreadsheet.editor.LoadEditorSettings(sheet.get('content').substring(parts.edit.start, parts.edit.end));
                }
                spreadsheet.InitializeSpreadsheetControl(root.attr('id'), me.$el.height() - 50, me.$el.width(), 0);
                sheet.collection.each(function (i) { i.trigger('recalc'); });
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
            me.listenTo(sheet, 'recalc', function () {
                loadQueue.queue('fx', function () {
                    if (me.spreadsheet) {
                        me.spreadsheet.ExecuteCommand('recalc', '');
                        me.spreadsheet.ExecuteCommand('redisplay', '');
                    }
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