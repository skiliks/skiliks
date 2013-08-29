/*global _, Backbone, SKApp, $*/
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
            'click .btn-simulation-stop-logs': 'doSimStopAndShowLogs'
        },

        /**
         * Constructor
         * @method initialize
         */
        'initialize': function () {
            try {
                this.render();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        'render': function () {
            try {
                this.$el.html(_.template(debug_template, {}));
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param event
         */
        'doSetTime': function (event) {
            try {
                var target = event.currentTarget;
                event.preventDefault();
                var hour = parseInt($(target).attr('data-hour'), 10);
                var minute = parseInt($(target).attr('data-minute'), 10);
                if (hour * 60 + minute <= SKApp.simulation.getGameMinutes()) {
                    alert('Путешествия во времени запрещены!');
                    return;
                }
                SKApp.simulation.setTime(hour, minute);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param event
         */
        'doLoadDocs': function (event) {
            try {
                SKApp.simulation.startPause(function(){});
                SKApp.simulation.documents.fetch();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param event
         */
        'doFormSetTime': function (event) {
            try {
                var target = event.currentTarget;
                event.preventDefault();
                var hours = parseInt(target.elements.hours.value, 10);
                var minutes = parseInt(target.elements.minutes.value, 10);
                if (hours * 60 + minutes <= SKApp.simulation.getGameMinutes()) {
                    alert('Путешествия во времени запрещены!');
                    return;
                }
                SKApp.simulation.setTime(hours, minutes);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param event
         */
        'doEventTrigger': function (event) {
            try {
                var me = this;
                var target = event.currentTarget;
                event.preventDefault();
                SKApp.simulation.events.triggerEvent(
                    target.elements.code.value,
                    target.elements.delay.value,
                    false,
                    false,
                    function (data) {
                        if (data.result) {
                            // window.scrollTo(0, 0);
                            me.$('form.trigger-event')
                                .append('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>Задача добавлена а очередь!</div>');
                        } else {
                            me.$('form.trigger-event')
                                .append('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>Задача НЕ добавлена а очередь!</div>');
                        }
                        $('.debug-panel form.trigger-event .alert').css('position', 'static');
                        me.$('form.trigger-event .alert').fadeOut(4000);
                    }
                );
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }

        },

        /**
         * @method
         */
        doSimStopAndShowLogs: function () {
            try {
                SKApp.set('result-url', '/admin/displayLog/' + SKApp.simulation.id);
                SKApp.simulation.onFinishTime();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        doUpdateEventsList: function(eventsQueue) {
            try {
                var me = this;
                var color = '#dddddd';

                // clean up list
                me.$('table#events-queue-table tbody').html('');

                me.$("#events-queue-clock").text($('.main-screen-stat .time').text());

                _.each(eventsQueue, function(item, key) {
                    if (item.isMail) {
                        color = '#ffffda';
                    } else {
                        color = '#dddddd';
                    }
                    me.$('table#events-queue-table tbody').append(
                        '<tr class="' + item.code + '-event" style="background-color:' + color + '">'
                        + '<td class="event-time">' + item.time + '</td>'
                        + '<td class="event-code">' + item.code + '</td>'
                        + '<td class="event-title">' + item.title + '</td>'
                        + '</tr>'
                    );
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }

    });

    return SKDebugView;
});
