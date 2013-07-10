/*global SocialCalc, _*/
define([], function () {
    "use strict";
    var loadQueue = $({});
    var sc = new SocialCalc.SpreadsheetControl();

    var SKSheetView = Backbone.View.extend({
        initialize:      function () {
            var sheet = this.options.sheet;
            this.parts = sc.DecodeSpreadsheetSave(sheet.get('content'));
            if (this.parts && this.parts.sheet) {
                this.sheetData = sheet.get('content').substring(this.parts.sheet.start, this.parts.sheet.end);
                SocialCalc.Formula.AddSheetToCache(sheet.collection.document.get('name'), sheet.get('name'), this.sheetData);
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
                var editorID = _.uniqueId('tableeditor-'),
                    spreadsheet = me.spreadsheet = new SocialCalc.SpreadsheetControl(editorID + '-');

                spreadsheet.editor.idPrefix = editorID + '-';
                spreadsheet.editor.document = sheet.collection.document;
                spreadsheet.editor.StatusCallback.continue_queue = {
                    func: function (object, cmdtype) {
                        if ( me.dequeue || !me.is_loaded && cmdtype === "doneposcalc") {
                            me.is_loaded = true;
                            loadQueue.dequeue('fx');
                        }

                        if (cmdtype === "renderdone") {
                            var spreadsheet_data = me.spreadsheet.CreateSpreadsheetSave();
                            var parts = sc.DecodeSpreadsheetSave(spreadsheet_data);
                            var sheet_data = spreadsheet_data.substring(parts.sheet.start, parts.sheet.end);
                            if (sheet.get('content') === spreadsheet_data) {
                                return;
                            }
                            sheet.set('content', spreadsheet_data);
                            sheet.save();
                            //SKApp.simulation.documents.fetch();
                            SocialCalc.Formula.AddSheetToCache(sheet.collection.document.get('name'), sheet.get('name'), sheet_data);
                            sheet.collection.each(function (element) {
                                if (element !== sheet) {
                                    element.trigger('recalc');
                                }
                            });
                        }
                    }
                };

                var root = $('<div></div>').addClass('table-editor').attr('id', editorID);
                me.rootView = root;
                root.appendTo(me.$el);
                me.setElement(root);

                spreadsheet.ParseSheetSave(me.sheetData);
                spreadsheet.InitializeSpreadsheetControl(root.attr('id'), me.$el.height(), me.$el.width(), 0);
                if (me.parts && me.parts.edit) {
                    spreadsheet.editor.LoadEditorSettings(sheet.get('content').substring(me.parts.edit.start, me.parts.edit.end));
                }

                /*sheet.collection.each(function (i) {
                    i.trigger('recalc');
                });*/
                me.spreadsheet.ExecuteCommand('recalc', '');
                me.spreadsheet.ExecuteCommand('redisplay', '');

                root.hide();
            });
        }
    }, {});
    return SKSheetView;
});