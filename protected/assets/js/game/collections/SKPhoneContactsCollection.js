/*global SKApp, Backbone, _, SKTodoTask */
var SKPhoneContactsCollection;
(function () {
    "use strict";
    /**
     * @class SKPhoneContactsCollection
     * @augments Backbone.Collection
     */
    SKPhoneContactsCollection = Backbone.Collection.extend({
        /**
         * @property model
         * @type SKPhoneContact
         * @default SKPhoneContact
         */
        model: SKPhoneContact,

        /**
         * @method parse
         * @param data
         * @returns array
         */
        parse: function (data) {
            return _.values(data.data);
        },

        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        sync: function (method, collection, options) {
            if ('read' === method) {
                SKApp.server.api('phone/getContacts', {}, function (data) {
                    options.success(data);
                });
            }
        }
    });

    return SKPhoneContactsCollection;
})();