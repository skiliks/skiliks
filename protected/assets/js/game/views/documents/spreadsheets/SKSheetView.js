/*global SocialCalc, _*/
define([], function () {
    "use strict";

    /**
     * Используется для вызова нескольких функций обработки листа подряд.
     * На мой взгляд - это усложнение на пустом месте.
     *
     * @type {*|jQuery|HTMLElement}
     */
    var loadQueue = $({});

    /** @var SocialCalc sc, SocialCalc instance */
    var sc = new SocialCalc.SpreadsheetControl();

    var SKSheetView = Backbone.View.extend({

        /**
         * Constructor
         * @method initialize
         */
        initialize: function () {
            try {
                var sheet = this.options.sheet;

                // SC иногда подвисает
                //
                sheet.set('isInitializeCalculated', false);
                var longLoadingTimeout = setTimeout(function() {
                    if (false == sheet.get('isInitializeCalculated')) {
                        if (window.Raven) {
                            window.Raven.captureMessage(
                                'Sheet ' + sheet.get('name') + ' in simulation ' + SKApp.simulation.id + ' завис.'
                            );
                        }
                    }
                }, 10000);

                this.parts = sc.DecodeSpreadsheetSave(sheet.get('content'));
                if (this.parts && this.parts.sheet) {
                    this.sheetData = sheet.get('content').substring(this.parts.sheet.start, this.parts.sheet.end);
                    SocialCalc.Formula.AddSheetToCache(sheet.collection.document.get('name'), sheet.get('name'), this.sheetData);
                }

                sheet.set('isInitializeCalculated', true);
                // clearTimeout(longLoadingTimeout);

                this.listenTo(sheet, 'activate', this.activateSheet);
                this.listenTo(sheet, 'recalc', this.recalcSheet);
                this.listenTo(sheet, 'deactivate', this.deactivateSheet);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Вызывается при переключении вкладок екселя
         */
        deactivateSheet: function () {
            try {
                var me = this;
                // зачем так сложно, чедь можно просто вызвать me.rootView.hide() ?
                // но ладно
                loadQueue.queue('fx', function () {
                    me.rootView.hide();
                    loadQueue.dequeue('fx');
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Вызывается после каждой правки на листе
         */
        recalcSheet:     function () {
            try {
                var me = this;
                var sheet = this.options.sheet;

                sheet.set('isRecalcCalculated', false);
                var longLoadingTimeout = setTimeout(function() {
                    if (false == sheet.get('isRecalcCalculated')) {
                        if (window.Raven) {
                            window.Raven.captureMessage(
                                'Sheet ' + sheet.get('name') + ' in simulation ' + SKApp.simulation.id + ' завис.'
                            );
                        }
                    }
                }, 10000);

                loadQueue.queue('fx', function () {
                    if (me.spreadsheet) {
                        me.spreadsheet.ExecuteCommand('recalc', '');
                        me.spreadsheet.ExecuteCommand('redisplay', '');
                    }
                    loadQueue.dequeue('fx');
                });

                sheet.set('isRecalcCalculated', true);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Вызывается при переключении вкладок екселя
         */
        activateSheet:   function () {
            try {
                var me = this;
                var sheet = this.options.sheet;

                sheet.set('isActivateCalculated', false);
                var longLoadingTimeout = setTimeout(function() {
                    if (false == sheet.get('isActivateCalculated')) {
                        if (window.Raven) {
                            window.Raven.captureMessage(
                                'Sheet ' + sheet.get('name') + ' in simulation ' + SKApp.simulation.id + ' завис.'
                            );
                        }
                    }
                }, 10000);

                loadQueue.queue('fx', function () {
                    me.rootView.show();
                    me.dequeue = true;
                    me.spreadsheet.editor.SchedulePositionCalculations();
                });

                sheet.set('isActivateCalculated', true);
                // clearTimeout(longLoadingTimeout)

            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Рендер {this.options.sheet} листа ексель
         */
        render: function () {
            try {
                var me = this;
                var sheet = this.options.sheet;

                loadQueue.queue('fx', function () {
                    var editorID = sheet.get('editor_id'),
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

                    me.spreadsheet.ExecuteCommand('recalc', '');
                    me.spreadsheet.ExecuteCommand('redisplay', '');

                    root.hide();
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    }, {});
    return SKSheetView;
});