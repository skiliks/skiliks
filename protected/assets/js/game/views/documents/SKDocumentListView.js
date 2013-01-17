/*global SKWindow, _, SKWindowView, SKConfig, SKApp, SKPhoneContactsCollection
 */

(function () {
    "use strict";

    window.SKDocumentsListView = SKWindowView.extend({
        title:'Мои документы',
        addClass:'documents-list',
        renderContent:function (el) {
            el.elfinder({
                url:'myDocuments/connector',
                transport:{
                    send:function (options) {
                        return SKApp.server.api('myDocuments/connector', options.data);

                    }
                },
                getFileCallback:function (file) {
                    var window = new SKDocumentsWindow({subname:'documentsFiles', filename:file});
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