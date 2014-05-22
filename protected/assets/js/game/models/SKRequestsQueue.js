/*global Backbone:false, console, SKApp, session, define */
define([], function() {
    "use strict";
    /**
     * @class SKRequestsQueue
     * @augments Backbone.Model
     */
    var SKRequestsQueue = Backbone.Model.extend({

        /**
         * number .uniqueId
         * boolean .is_repeat_request,
         * string .url
         * function .callback, нужет только есть в резутьтате запроса должна вызывать функция - сюда её и сохраняем
         * array .ajax, ajax response
         * string .status, 'pending', 'failed'
         */

    });
    return SKRequestsQueue;
});