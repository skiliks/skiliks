/*global Backbone, _, SKEvent, SKApp*/
(function () {
    "use strict";
    window.SKEventCollection = Backbone.Collection.extend({
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
        'getByTypeSlug':function (type, completed) {
            return this.filter(function (event) {
                if ((completed !== undefined ) && (event.completed !== completed)) {
                    return false;
                }
                return (event.getTypeSlug() === type);
            });
        },
        'triggerEvent': function (code, delay, clear_events, clear_assessment) {
            SKApp.server.api('events/start',{
                eventCode:code,
                delay:delay,
                clearEvents:clear_events,
                clearAssessment:clear_assessment
            }, function () {
                window.scrollTo(0,0);
            });
        }
    });
})();