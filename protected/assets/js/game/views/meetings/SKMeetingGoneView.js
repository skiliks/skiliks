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
                subject = me.options.model_instance.get('subject');

            $el.html(_.template(meeting_gone_tpl, {
                'subject': subject
            }));
        },

        doProceedWork: function() {
            SKApp.simulation.stopPause();
            this.options.model_instance.close();
        }
    });

    return SKMeetingView;
});