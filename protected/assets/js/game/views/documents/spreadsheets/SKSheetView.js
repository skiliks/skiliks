/*global SocialCalc, _*/
define([], function () {
    "use strict";
    var loadQueue = $({});
    var sc = new SocialCalc.SpreadsheetControl();

    var SKSheetView = Backbone.View.extend({
        initialize:      function () {
            var sheet = this.options.sheet;
            var parts = sc.DecodeSpreadsheetSave(sheet.get('content'));
            if (parts && parts.sheet) {
                var sheet_data = sheet.get('content').substring(parts.sheet.start, parts.sheet.end);
                SocialCalc.Formula.AddSheetToCache(sheet.get('name'), sheet_data);
            }

            this.listenTo(sheet, 'activate', this.activateSheet);
            this.listenTo(sheet, 'recalc', this.recalcSheet);
            this.listenTo(sheet, 'deactivate', this.deactivateSheet);
        },
        updateRelatedSheets: function () {

        },
        deactivateSheet: function () {
            var me = this;
            loadQueue.queue('fx', function () {
                me.rootView.hide();
                loadQueue.dequeue('fx');
            });
        },
        recalcSheet:     function () {
            var me = this;
            loadQueue.queue('fx', function () {
                if (me.spreadsheet) {
                    me.spreadsheet.ExecuteCommand('recalc', '');
                    me.spreadsheet.ExecuteCommand('redisplay', '');
                }
                loadQueue.dequeue('fx');

            });
        },
        activateSheet:   function () {
            var me = this;
            var sheet = this.options.sheet;

            loadQueue.queue('fx', function () {
                me.rootView.show();
                me.dequeue = true;
                me.spreadsheet.editor.SchedulePositionCalculations();
            });
        },
        /**
         *
         * @param {SKSheet} sheet
         */
        render:          function () {
            var me = this;
            var sheet = this.options.sheet;

            loadQueue.queue('fx', function () {
                console.log('sheet init');
                var editorID = _.uniqueId('tableeditor-');
                me.spreadsheet = new SocialCalc.SpreadsheetControl(editorID + '-');
                var spreadsheet = me.spreadsheet;
                spreadsheet.editor.idPrefix = editorID + '-';
                spreadsheet.editor.StatusCallback.continue_queue = {
                    func: function (object, cmdtype) {
                        if ( me.dequeue || !me.is_loaded && cmdtype === "doneposcalc") {
                            me.is_loaded = true;
                            loadQueue.dequeue('fx');
                        }

                        if (cmdtype === "doneposcalc") {
                            var spreadsheet_data = me.spreadsheet.CreateSpreadsheetSave();
                            var parts = sc.DecodeSpreadsheetSave(spreadsheet_data);
                            var sheet_data = spreadsheet_data.substring(parts.sheet.start, parts.sheet.end);
                            if (sheet.get('content') === spreadsheet_data) {
                                return;
                            }
                            sheet.set('content', spreadsheet_data);
                            sheet.save();
                            SKApp.simulation.documents.fetch();
                            SocialCalc.Formula.AddSheetToCache(sheet.get('name'), sheet_data);
                            sheet.collection.each(function (element) {
                                if (element !== sheet) {
                                    element.trigger('recalc');
                                }
                            });
                        }
                    }
                };
                var root = $('<div></div>')
                    .addClass('table-editor')
                    .attr('id', editorID);
                me.rootView = root;
                root.appendTo(me.$el);
                me.setElement(root);
                root.hide();

                var parts = me.spreadsheet.DecodeSpreadsheetSave(sheet.get('content'));
                console.log(sheet.get('content'));
                if (parts && parts.sheet) {
                    var sheet_data = sheet.get('content').substring(parts.sheet.start, parts.sheet.end);
                    me.spreadsheet.ParseSheetSave(sheet_data);
                }
                spreadsheet.InitializeSpreadsheetControl(root.attr('id'), me.$el.height() - 50, me.$el.width(), 0);
                if (parts && parts.edit) {
                    me.spreadsheet.editor.LoadEditorSettings(sheet.get('content').substring(parts.edit.start, parts.edit.end));
                }
                sheet.collection.each(function (i) {
                    i.trigger('recalc');
                });
                me.spreadsheet.ExecuteCommand('recalc', '');
                me.spreadsheet.ExecuteCommand('redisplay', '');

            });


        }
    }, {});
    return SKSheetView;
});