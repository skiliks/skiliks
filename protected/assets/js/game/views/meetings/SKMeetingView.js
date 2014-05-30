/* global define, $, _, SKApp, AppView */

/**
* @class SKMeetingView
* @augments Backbone.View
*/
var SKMeetingView;

define([
    'game/views/SKWindowView',
    'game/collections/SKMeetingSubjectCollection',
    'game/views/SKDialogView',

    'text!game/jst/meeting/select.jst'
], function (
        SKWindowView,
        SKMeetingSubjectCollection,
        SKDialogView,

        meetingChooseTpl
    ) {
    "use strict";

    SKMeetingView = SKWindowView.extend({

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        'events':_.defaults({
            'click .meeting-subject': 'leave'
        }, SKWindowView.prototype.events),

        /**
         * Стандартный родительский метод
         */
        dimensions: {
            width: 700,
            height: 500
        },

        /**
         * Конструктор
         */
        initialize: function() {
            try {
                this.listenTo(this.options.model_instance, 'close', function() {
                    AppView.frame._hidePausedScreen();
                });

                SKWindowView.prototype.initialize.call(this);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Стандартный родительский метод
         */
        'renderWindow': function (el) {
            try {
                AppView.frame._showPausedScreen();
                var me = this;

                me.subjects = new SKMeetingSubjectCollection();
                me.subjects.fetch();

                me.subjects.on('reset', function () {
                    // если ответ пришел после того как челокек успел начать диалог
                    // то отрисовывается дверь - это неправильно
                    if (false == SKApp.simulation.events.isNowDialogInProgress(null)) {
                        el.html(_.template(meetingChooseTpl, {
                            'subjects': me.subjects
                        }));
                        me.$el.topZIndex();
                    } else {
                        AppView.frame._hidePausedScreen();
                    }
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Стандартный родительский метод
         */
        doActivate: function () {},

        /**
         * Обработка клика Уйти на митинг "ХХХ".
         * @param OnClickEvent e
         */
        'leave': function (e) {
            try {
                var subjectId = $(e.currentTarget).attr('data-subject-id'),
                    subject = this.subjects.get(subjectId),
                    simulation = SKApp.simulation;

                this.options.model_instance.close();

                simulation.startPause(function() {});
                SKApp.server.api('meeting/leave', {'id': subjectId});
                if (subject.get('duration') == 0) {
                    window.AppView.frame.stopSimulation();
                }
                else {
                    SKApp.simulation.window_set.open('visitor', 'meetingGone', {
                        'subject': subject,
                        'params': {meetingId: subjectId}
                    });
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKMeetingView;
});