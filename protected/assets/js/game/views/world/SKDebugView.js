/*global _, Backbone, SKApp*/
var SKDebugView;
define(["text!game/jst/simulation/debug.jst"], function (debug_template) {
    "use strict";
    /**
     * @class SKDebugView
     * @augments Backbone.View
     */
    SKDebugView = Backbone.View.extend({
        'events': {
            'click .set-time': 'doSetTime',
            'submit .form-set-time': 'doFormSetTime',
            'submit .trigger-event': 'doEventTrigger',
            'click .btn-load-documents': 'doLoadDocs',
            'click .btn-simulation-stop-logs': 'doSimStopAndLoadLogs',
            'click .send-email-ms': 'doSendMs'
        },

        /**
         * Constructor
         * @method initialize
         */
        'initialize': function () {
            this.render();
        },

        /**
         * @method
         */
        'render': function () {
            this.$el.html(_.template(debug_template, {}));
        },

        /**
         * @method
         * @param event
         */
        'doSetTime': function (event) {
            var target = event.currentTarget;
            event.preventDefault();
            var hour = $(target).attr('data-hour');
            var minute = $(target).attr('data-minute');
            SKApp.user.simulation.setTime(hour, minute);
        },

        /**
         * @method
         * @param event
         */
        'doLoadDocs': function (event) {
            SKApp.user.simulation.documents.fetch();
        },

        /**
         * @method
         * @param event
         */
        'doFormSetTime': function (event) {
            var target = event.currentTarget;
            event.preventDefault();
            var hours = target.elements.hours.value;
            var minutes = target.elements.minutes.value;
            SKApp.user.simulation.setTime(hours, minutes);
        },

        /**
         * @method
         * @param event
         */
        'doEventTrigger': function (event) {
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
                        me.$('form.trigger-event')
                            .append('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Задача добавлена а очередь!</div>');
                    } else {
                        me.$('form.trigger-event')
                            .append('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Задача НЕ добавлена а очередь!</div>');
                    }
                    me.$('form.trigger-event .alert').fadeOut(4000);
                }
            );

        },

        /**
         * @method
         */
        doSimStopAndLoadLogs: function () {
            SKApp.user.simulation.set('result-url', '/admin/displayLog?simulation=' + SKApp.user.simulation.id);
            SKApp.user.stopSimulation();
        },

        /**
         * @method
         * @param event
         */
        doSendMs: function (event) {
            event.preventDefault(event);
            event.stopPropagation(event);

            var target = event.currentTarget;

            SKApp.server.api(
                'mail/sendMsInDevMode',
                {
                    msCode: $(target).attr('data-code'),
                    time: SKApp.user.simulation.getGameSeconds(),
                    windowId: SKApp.user.simulation.window_set.getActiveWindow().getWindowId(),
                    subWindowId: SKApp.user.simulation.window_set.getActiveWindow().getSubwindowId(),
                    windowUid: SKApp.user.simulation.window_set.getActiveWindow().window_uid
                },
                function (response) {
                    if (response.result) {
                        // Oh no, please, don't insert this :(
                        $('body form.trigger-event').append('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Письмо "' + $(target).attr('data-code') + '" отправлено!</div>');
                        window.scrollTo(0, 0);
                    } else {
                        $('body form.trigger-event').append('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Письмо "' + $(target).attr('data-code') + '" НЕ отправлено!</div>');
                    }
                    $('body form.trigger-event .alert').fadeOut(4000);
                }
            );
        }
    });

    return SKDebugView;
});
