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
            var hour = parseInt($(target).attr('data-hour'), 10);
            var minute = parseInt($(target).attr('data-minute'), 10);
            if (hour * 60 + minute <= SKApp.simulation.getGameMinutes()) {
                alert('Путешествия во времени запрещены!');
                return;
            }
            SKApp.simulation.setTime(hour, minute);
        },

        /**
         * @method
         * @param event
         */
        'doLoadDocs': function (event) {
            SKApp.simulation.documents.fetch();
        },

        /**
         * @method
         * @param event
         */
        'doFormSetTime': function (event) {
            var target = event.currentTarget;
            event.preventDefault();
            var hours = parseInt(target.elements.hours.value, 10);
            var minutes = parseInt(target.elements.minutes.value, 10);
            if (hours * 60 + minutes <= SKApp.simulation.getGameMinutes()) {
                alert('Путешествия во времени запрещены!');
                return;
            }
            SKApp.simulation.setTime(hours, minutes);
        },

        /**
         * @method
         * @param event
         */
        'doEventTrigger': function (event) {
            var me = this;
            var target = event.currentTarget;
            event.preventDefault();
            SKApp.simulation.events.triggerEvent(
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
            SKApp.simulation.set('result-url', '/admin/displayLog/' + SKApp.simulation.id);
            AppView.frame.stopExitProtection();
            SKApp.simulation.stop();
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
                    time: SKApp.simulation.getGameSeconds(),
                    windowId: SKApp.simulation.window_set.getActiveWindow().getWindowId(),
                    subWindowId: SKApp.simulation.window_set.getActiveWindow().getSubwindowId(),
                    windowUid: SKApp.simulation.window_set.getActiveWindow().window_uid
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
