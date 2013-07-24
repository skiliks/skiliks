/* global define, $, _, SKApp */

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

        'renderWindow': function (el) {
            var me = this;

            this.subjects = new SKMeetingSubjectCollection();
            this.subjects.fetch();

            this.subjects.on('reset', function () {
                el.html(_.template(meetingChooseTpl, {
                    'subjects': me.subjects
                }));
            });
        },

        'leave': function (e) {
            var subjectId = $(e.currentTarget).attr('data-subject-id'),
                subject = this.subjects.get(subjectId),
                simulation = SKApp.simulation;

            this.options.model_instance.close();

            simulation.startPause(function() {});
            SKApp.server.api('meeting/leave', {'id': subjectId});

            SKApp.simulation.window_set.open('visitor', 'meetingGone', {
                'subject': subject,
                'params': {meetingId: subjectId}
            });
        }
    });

    return SKMeetingView;
});