/* global define, $, _, SKApp */

/**
 * @class SKMeetingGoneView
 * @augments Backbone.View
 */
var SKMeetingGoneView;

define([
    'game/views/SKWindowView'
], function (
        SKWindowView
    ) {
    "use strict";

    SKMeetingGoneView = SKWindowView.extend({

        'events':_.defaults({

        }, SKWindowView.prototype.events),

        /*
            We will not use any specific rendering, just instantiate standard dialog
         */
        'renderWindow': function (el) {
            var me = this,
                subject = me.options.model_instance.get('subject');

            me.dialog = new SKDialogView({
                message: subject.get('description') + '. Это заняло ' + subject.get('duration') + ' мин',
                buttons: [{
                    id: 'ok',
                    value: 'Продолжить работу',
                    onclick: function() {
                        me.options.model_instance.close();
                        SKApp.simulation.stopPause();
                    }
                }]
            });
        }
    });

    return SKMeetingView;
});