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

        'events':_.defaults({
            'click .meeting-subject': 'leave'
        }, SKWindowView.prototype.events),

        dimensions: {
            width: 700,
            height: 500
        },

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

        'renderWindow': function (el) {
            try {
                var me = this;

                me.subjects = new SKMeetingSubjectCollection();
                me.subjects.fetch();

                me.subjects.on('reset', function () {
                    el.html(_.template(meetingChooseTpl, {
                        'subjects': me.subjects
                    }));
                    AppView.frame._showPausedScreen();
                    me.$el.topZIndex();
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        doActivate: function () {},

        'leave': function (e) {
            try {
                var subjectId = $(e.currentTarget).attr('data-subject-id'),
                    subject = this.subjects.get(subjectId),
                    simulation = SKApp.simulation;

                this.options.model_instance.close();

                simulation.startPause(function() {});
                SKApp.server.api('meeting/leave', {'id': subjectId});
                if ('Пойти домой' == subject.get('label')) {
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