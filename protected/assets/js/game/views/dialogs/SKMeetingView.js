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
                subject = this.subjects.get(subjectId);

            SKApp.server.api('meeting/leave', {'id': subjectId});

            new SKDialogView({
                message: subject.get('description') + '. Это заняло ' + subject.get('duration') + ' мин',
                buttons: [{
                    id: 'ok',
                    value: 'Продолжить работу'
                }]
            });

            this.options.model_instance.close();
        }
    });

    return SKMeetingView;
});