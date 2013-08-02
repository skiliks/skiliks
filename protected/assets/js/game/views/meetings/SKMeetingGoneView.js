/* global define, $, _, SKApp, AppView */

/**
 * @class SKMeetingGoneView
 * @augments Backbone.View
 */
var SKMeetingGoneView;

define([
    'game/views/SKWindowView',

    'text!game/jst/meeting/gone.jst'
], function (
        SKWindowView,

        meeting_gone_tpl
    ) {
    "use strict";

    SKMeetingGoneView = SKWindowView.extend({

        'events':_.defaults({
            'click .proceed-btn': 'doProceedWork'
        }, SKWindowView.prototype.events),

        isDisplaySettingsButton: false,

        isDisplayCloseWindowsButton: false,

        title: 'Системное сообщение',

        dimensions: {
            width: 600,
            height: 270
        },

        initialize: function() {
            this.listenTo(this.options.model_instance, 'close', function() {
                AppView.frame._hidePausedScreen();
            });

            SKWindowView.prototype.initialize.call(this);
        },

        renderContent: function ($el) {
            var subject = this.options.model_instance.get('subject'),
                time;

            var duration = subject.get('duration');
            var buttonLabel = 'Продолжить работу';

            if ('Пойти домой' == subject.get('label')) {
                duration = 0;
                buttonLabel = 'Продолжить';
            }

            time = SKApp.simulation.getGameMinutes() + parseInt(duration, 10);
            time = Math.floor(time / 60) + ':' + (time % 60 < 10 ? '0' : '') + time % 60;

            $el.html(_.template(meeting_gone_tpl, {
                'subject': subject,
                'returnTime': time,
                'buttonLabel': buttonLabel
            }));

            AppView.frame._showPausedScreen();
            this.$el.topZIndex();
        },

        doProceedWork: function(e) {
            var simulation = SKApp.simulation,
                subject = this.options.model_instance.get('subject'),
                me = this;

            simulation.stopPause(function() {

                var duration = subject.get('duration');

                if ('Пойти домой' == subject.get('label')) {
                    window.AppView.frame.stopSimulation();
                }

                simulation.skipped_seconds += (duration * 60) / SKApp.get('skiliksSpeedFactor');
                simulation.trigger('tick');
                me.options.model_instance.close();
            });
        }
    });

    return SKMeetingView;
});