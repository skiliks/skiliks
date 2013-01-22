/*global _, Backbone, SKApp*/
(function () {
    "use strict";
    window.SKDebugView = Backbone.View.extend({
        'initialize':function () {
            this.render();
        },
        'events':{
            'click .set-time':'doSetTime',
            'submit .form-set-time':'doFormSetTime',
            'submit .trigger-event':'doEventTrigger'
        },
        'render':function () {
            this.$el.html(_.template($('#debug_panel').html(), {}));
        },
        'doSetTime':function (event) {
            var target = event.currentTarget;
            event.preventDefault();
            var hour = $(target).attr('data-hour');
            var minute = $(target).attr('data-minute');
            SKApp.user.simulation.setTime(hour, minute);
        },
        'doFormSetTime':function (event) {
            var target = event.currentTarget;
            event.preventDefault();
            var hours = target.elements.hours.value;
            var minutes = target.elements.minutes.value;
            SKApp.user.simulation.setTime(hours, minutes);
        },
        'doEventTrigger':function (event) {
            var target = event.currentTarget;
            event.preventDefault();
            console.log(target);
            console.log(target.elements);
            console.log(target.elements.clear_events.value);
            SKApp.user.simulation.events.triggerEvent(
                target.elements.code.value,
                target.elements.delay.value,
                target.elements.clear_events.value,
                target.elements.clear_assessment.value, function (data) {
                    if (data.result) {
                        window.scrollTo(0, 0);
                    }
                }
            );

        }
    });
})();