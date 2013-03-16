/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection, SKDocumentsWindow
 */

/**
 * @class SKDocumentsListView
 * @augments window.SKWindowView
 */
var SKDocumentsListView;
define(["game/views/SKWindowView", "game/models/window/SKDocumentsWindow"], function () {
    "use strict";
    var SKElFinderTransport = {
        /**
         *
         * @param fm
         */
        init: function (fm) {
            this.fm = fm;
        },

        /**
         * @method
         * @param options
         * @returns {{done: Function, fail: Function}}
         */
        send: function (options) {
            var res = {
                'done': function (cb) {
                    cb(this.data);
                    return this;
                },
                'fail': function (cb) {
                    // No fails :)
                    return this;
                }
            };
            var cmd = options.data.cmd;
            if ('open' === cmd) {
                if (this.fm.url(options.data.target) === "") {
                    res.data = {
                        cwd: {
                            mime: "directory",
                            name: "Мои документы",
                            hash: "s1_Lw",
                            volumeid: "s1_"
                        },
                        read: 1,
                        write: 0,
                        files: [],
                        options: {
                            url: '/documents/templates/',
                            separator: '/',
                            path: 'Мои документы',
                            archivers: {
                                create: [],
                                extract: []
                            }
                        }
                    };
                    res.data.files.push({
                        mime: "directory",
                        name: "Мои документы",
                        phash: "s1_Lw",
                        read: 1
                    });
                    SKApp.simulation.documents.each(function (model) {
                        res.data.files.push({
                            mime: model.get('mime'),
                            name: model.get('name'),
                            hash: model.get('name'),
                            phash: "s1_Lw",
                            read: 1,
                            write: 1
                        });
                    });
                }
            } else {
                throw 'method not impremented';
            }
            return res;

        }
    };

    /**
     *
     * @type {*}
     */
    SKDocumentsListView = SKWindowView.extend(
        /** @lends SKDocumentsListView.prototype */
        {
            title: 'Мои документы',
            addClass: 'documents-list',

            dimensions: {
                width: 800,
                height: 400
            },

            /**
             * @method
             * @param {jQuery} el
             */
            renderContent: function (el) {
                var me = this;
                el.elfinder({
                    url: 'myDocuments/connector',
                    transport: SKElFinderTransport,
                    getFileCallback: function (file) {
                        file = decodeURIComponent(file);
                        file = file.replace(/.*\//, '');
                        var document = SKApp.simulation.documents.where({name: file})[0];
                        var window = new SKDocumentsWindow({
                            subname: 'documentsFiles',
                            document: document,
                            fileId: document.get('id')
                        });
                        window.open();
                    },
                    ui: ['toolbar', 'places', 'path', 'stat'],
                    uiOptions: {
                        // toolbar configuration
                        toolbar: [
                            ['back', 'forward'],
                            // ['reload'],
                            // ['home', 'up'],
                            ['info'],
                            ['quicklook'],
                            ['view']
                        ],

                        navbar: {
                            minWidth: 150,
                            maxWidth: 500
                        }
                    },
                    lang: 'ru',
                    resizable: false
                }).elfinder('instance');
            }
        });
});