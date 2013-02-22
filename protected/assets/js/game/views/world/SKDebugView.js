/*global _, Backbone, SKApp*/
var SKDebugView;
define(["text!game/jst/simulation/debug.jst"], function (debug_template) {
    "use strict";
    /** @class */
    SKDebugView = Backbone.View.extend(
        /** @lends SKDebugView.prototype */
        {
            'initialize':function () {
                this.render();
            },
            'events':{
                'click .set-time':'doSetTime',
                'submit .form-set-time':'doFormSetTime',
                'submit .trigger-event':'doEventTrigger',
                'click .btn-load-documents':'doLoadDocs'
            },
            'render':function () {
                this.$el.html(_.template(debug_template, {}));
            },
            'doSetTime':function (event) {
                var target = event.currentTarget;
                event.preventDefault();
                var hour = $(target).attr('data-hour');
                var minute = $(target).attr('data-minute');
                SKApp.user.simulation.setTime(hour, minute);
            },
            'doLoadDocs':function (event) {
                SKApp.user.simulation.documents.fetch();
            },
            'doFormSetTime':function (event) {
                var target = event.currentTarget;
                event.preventDefault();
                var hours = target.elements.hours.value;
                var minutes = target.elements.minutes.value;
                SKApp.user.simulation.setTime(hours, minutes);
            },
            'doEventTrigger':function (event) {
                var me = this;
                var target = event.currentTarget;
                event.preventDefault();
                SKApp.user.simulation.events.triggerEvent(
                    target.elements.code.value,
                    target.elements.delay.value,
                    target.elements.clear_events.value,
                    target.elements.clear_assessment.value,
                    function (data) {
                        if (data.result) {
                            window.scrollTo(0, 0);
                            me.$('form.trigger-event').append('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Задача добавлена а очередь!</div>');
                        } else {
                            me.$('form.trigger-event').append('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Задача НЕ добавлена а очередь!</div>');
                        }
                        me.$('form.trigger-event .alert').fadeOut(4000);
                    }
                );

            }
        });
});