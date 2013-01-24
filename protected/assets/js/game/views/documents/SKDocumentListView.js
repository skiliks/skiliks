/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection, SKDocumentsWindow
 */

(function () {
    "use strict";

    window.SKDocumentsListView = SKWindowView.extend({
        title:'Мои документы',
        addClass:'documents-list',
        renderContent:function (el) {
            var me = this;
            el.elfinder({
                url:'myDocuments/connector',
                transport:{
                    init: function(fm) {
                        this.fm = fm;
                    },
                    send:function (options) {
                        var res = {
                            'done': function (cb) {
                                cb(this.data);
                                return this;
                            },
                            'fail': function(cb) {
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
                                    SKApp.user.simulation.documents.each(function (model) {
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
                },
                getFileCallback:function (file) {
                    file = decodeURIComponent(file);
                    file = file.replace(/.*\//,'');
                    var fileId = SKApp.user.simulation.documents.where({name:file})[0].get("id");
                    var window = new SKDocumentsWindow({subname:'documentsFiles', filename:file, fileId:fileId});
                    window.open();
                },
                uiOptions:{
                    // toolbar configuration
                    toolbar:[
                        ['back', 'forward'],
                        // ['reload'],
                        // ['home', 'up'],
                        ['open', 'download', 'getfile'],
                        ['info'],
                        ['quicklook'],
                        ['search'],
                        ['view']
                    ],

                    navbar:{
                        minWidth:150,
                        maxWidth:500
                    }
                }
                // lang: 'ru',             // language (OPTIONAL)
            }).elfinder('instance');
        }
    });
})();