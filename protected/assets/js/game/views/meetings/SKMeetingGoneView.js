/* global define, $, _, SKApp */

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
            height: 200
        },

        renderContent: function ($el) {
            var me = this,
                subject = me.options.model_instance.get('subject'),
                time;

            time = SKApp.simulation.getGameMinutes() + parseInt(subject.get('duration'), 10);
            time = Math.floor(time / 60) + ':' + (time % 60 < 10 ? '0' : '') + time % 60;

            $el.html(_.template(meeting_gone_tpl, {
                'subject': subject,
                'returnTime': time
            }));
        },

        doProceedWork: function(e) {
            var simulation = SKApp.simulation,
                subject = this.options.model_instance.get('subject');

            simulation.stopPause(function() {
                simulation.skipped_seconds += subject.get('duration') * 60 / SKApp.get('skiliksSpeedFactor');
                simulation.trigger('tick');
            });

            this.options.model_instance.close();
        }
    });

    return SKMeetingView;
});