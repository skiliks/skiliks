/*global Backbone:false, console, SKApp, session, SKDocument */
var SKDocumentCollection;
define(["game/models/SKDocument"], function () {
    "use strict";
    /**
     * @class SKEventCollection
     * @augments Backbone.Collection
     */
    SKDocumentCollection = Backbone.Collection.extend({
        /**
         * @property model
         * @type SKDocument
         * @default SKDocument
         */
        model: SKDocument,

        /**
         * @property excelErrorHappened
         * @type boolean
         * @default false
         */
        excelErrorHappened: false,

        /**
         * Constructor
         * @method initialize
         * @return void
         */
        initialize: function() {
            var me = this;
            if (window.addEventListener){
                window.addEventListener("message", me.handlePostMessage,false);
            } else {
                window.attachEvent("onmessage", me.handlePostMessage);
            }

        },

        /**
         * @method handlePostMessage
         * @param postMessage event
         * @return void
         */
        handlePostMessage: function(event) {
            var me = this;

            console.log('handlePostMessage');
            if ((undefined != typeof event && event.origin !== "*") || true === me.excelErrorHappened) {
                console.log('event', event);
                me.excelErrorHappened = true;

                me.message_window = new SKDialogView({
                    'message': 'Excel выполнил недопустимую операцию. <br/> Необходимо закрыть и заново открыть документ.',
                    'buttons': [
                        {
                            'value': 'Подтвердить',
                            'onclick': function () {
                                SKApp.simulation.documents = new SKDocumentCollection();
                                me.excelErrorHappened = false;

                                delete me.message_window;
                            }
                        },
                        {
                            'value': 'Отмена',
                            'onclick': function () {
                                delete me.message_window;
                            }
                        }
                    ]
                });
            }
        },

        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        sync: function (method, collection, options) {
            if ('read' === method) {
                SKApp.server.api('myDocuments/getList', {}, function (data) {
                    options.success(data.data);
                });
            }
        }
    });

    return SKDocumentCollection;
});