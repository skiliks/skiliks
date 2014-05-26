/*global Backbone:false, console, SKApp, session */

define([], function () {
    "use strict";
    var SKSheet = Backbone.Model.extend({

        /**
         * Constructor
         */
        initialize: function () {
            try {
                this.on('change:content', function () {

                });
                this.set('editor_id', _.uniqueId('tableeditor-'));
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Меняем инфу о том какой лист активен в данном ексель документе.
         */
        activate: function () {
            try {
                var me = this;
                this.collection.each(function (sheet) {
                    if (sheet.get('name') === me.get('name')) {
                        sheet.set('active', true);
                        sheet.trigger('activate');
                    } else {
                        sheet.set('active', false);
                        sheet.trigger('deactivate');
                    }
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Синхронизация обьякта с бекендом
         * @param String method
         * @param SkSheetCollection collection
         * @param Array options
         */
        sync: function (method, collection, options) {
            try {
                var me = this;
                if ('create' === method) {
                // if ('update' === method) {
                    SKApp.server.api(
                        'myDocuments/saveSheet/' + this.collection.document.id,
                        {
                            'model-name': me.get('name'),
                            'model-content':  me.get('content')
                        },
                        function (data) {
                            options.success(data);
                        }
                    );
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
    return SKSheet;
});