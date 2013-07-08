/*global Backbone:false, console, SKApp, session, define */

define(['game/models/SKRequestsQueue'], function (SKRequestsQueue) {
    "use strict";
    /**
     *
     * @class SKRequestsQueueCollection
     */
    var SKRequestsQueueCollection = Backbone.Collection.extend({
        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        model: SKRequestsQueue
    });
    return SKRequestsQueueCollection;
});