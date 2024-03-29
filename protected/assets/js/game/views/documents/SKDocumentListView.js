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
         * Constructor
         * @param ? fm, FileManager?
         */
        init: function (fm) {
            this.fm = fm;
        },

        /**
         * @param Array. options
         * @returns {{done: Function, fail: Function}}
         */
        send: function (options) {
           try {
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
                    if(SKApp.server.is_active_game === false){ return; }
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
                }else if("reload"){
                    return res;
                } else {
                    throw new Error ('method not implemented');
                }
                return res;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    };

    SKDocumentsListView = SKWindowView.extend(
        /** @lends SKDocumentsListView.prototype */
        {
            /**
             * Переопределение поведения SKWindowView
             * @var Boolean
             */
            isDisplaySettingsButton:false,

            /**
             * Переопределение поведения SKWindowView
             * @var String
             */
            title: 'Мои документы',

            /**
             * Переопределение поведения SKWindowView
             * @var String
             */
            addClass: 'documents-list documents-window',

            /**
             * Переопределение поведения SKWindowView
             * @var String
             */
            addId: 'documents-window',

            /**
             * Переопределение поведения SKWindowView
             * @var Array.<Number>
             */
            dimensions: {
                width:  800,
                height: 400
            },

            /**
             * Стандартный родительский метод
             * @param {jQuery} el
             */
            renderContent: function (el) {
                try {
                    var me = this;
                    window.elfinderInstace = el.elfinder({
                        url: 'myDocuments/connector',
                        transport: SKElFinderTransport,
                        getFileCallback: function (file) {
                            if(SKApp.server.is_active_game === false){ return false; }
                            file = decodeURIComponent(file);
                            file = file.replace(/.*\//, '');
                            var document = SKApp.simulation.documents.where({name: file})[0];
                            var window = SKApp.simulation.window_set.where({subname: 'documentsFiles', fileId: document.get('id')})[0];
                            if (window !== undefined) {
                                window.setOnTop();
                            } else {
                                window = new SKDocumentsWindow({
                                    subname: 'documentsFiles',
                                    document: document,
                                    fileId: document.get('id')
                                });
                                window.open();
                            }
                        },
                        ui: ['places', 'path', 'stat'],
                        uiOptions: {
                            toolbar: [
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });
});