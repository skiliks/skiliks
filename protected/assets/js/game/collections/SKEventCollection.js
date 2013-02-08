/*global SKEventCollection:true, SKEvent, Backbone, _, SKApp*/
define(["game/models/SKEvent"], function () {
    "use strict";
    /**
     * @class List of events
     */
    window.SKEventCollection = Backbone.Collection.extend(
        /**
         * @lends SKEventCollection.prototype
         */
        {
            'model':SKEvent,
            'getUnreadMailCount':function (cb) {
                SKApp.server.api('mail/getInboxUnreadCount', {}, function (data) {
                    if (data.result === 1) {
                        var counter = data.unreaded;
                        cb(counter);
                    }
                });
            },
            'getPlanTodoCount':function (cb) {
                SKApp.server.api('todo/getCount', {}, function (data) {
                    if (data.result === 1) {
                        var counter = data.data;
                        cb(counter);
                    }
                });
            },

            /**
             * Returns true if simulation can accept event
             *
             * @param {SKEvent} event
             * @returns {Boolean}
             */
            canAddEvent:function (event) {
                if (!event.getTypeSlug().match(/(phone|visit)$/)) {
                    return true;
                }
                var res = true;
                this.each(function (ev) {
                    if (ev.getTypeSlug().match(/(phone|visit)$/) &&
                        (ev.getStatus() === 'in progress' || ev.getStatus() === 'waiting') &&
                        ev.get('data')[0].code !== event.get('data')[0].code ) {
                        res = false;
                    }
                });
                return res;
            },
            /**
             *
             * @param {string} code
             * @param {int} delay in minutes
             * @param clear_events
             * @param clear_assessment
             */
            'triggerEvent':function (code, delay, clear_events, clear_assessment) {
                var callback;
                if (arguments.length > 4) {
                    callback = arguments[4];
                } else {
                    callback = undefined;
                }
                SKApp.server.api('events/start', {
                    eventCode:code,
                    delay:delay,
                    clearEvents:clear_events,
                    clearAssessment:clear_assessment
                }, callback);
            }
        });
});