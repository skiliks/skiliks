/* global define, $, _, SKApp */

/**
 * @class SKVisitView
 * @augments Backbone.View
 */
var SKMeetingView;

define([
    'game/views/SKWindowView',
    'game/collections/SKMeetingSubjectCollection',
    'game/views/SKDialogView',

    'text!game/jst/visit/meeting.jst'
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


            simulation.startPause(function() {
                simulation.skipped_seconds += subject.get('duration') * 60 / SKApp.get('skiliksSpeedFactor');
                simulation.trigger('tick');

                SKApp.server.api('meeting/leave', {'id': subjectId}, function() {
                    var dialog = new SKDialogView({
                        message: subject.get('description') + '. Это заняло ' + subject.get('duration') + ' мин',
                        buttons: [{
                            id: 'ok',
                            value: 'Продолжить работу',
                            onclick: function() {
                                simulation.stopPause();
                            }
                        }]
                    });
                });
            });

            this.options.model_instance.close();
        }
    });

    return SKMeetingView;
});