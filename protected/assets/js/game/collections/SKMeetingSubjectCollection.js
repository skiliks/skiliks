/*global Backbone, define, SKApp */

var SKMeetingSubjectCollection;

define(["game/models/SKMeetingSubject"], function (SKMeetingSubject) {
    "use strict";

    SKMeetingSubjectCollection = Backbone.Collection.extend({

        model: SKMeetingSubject,

        parse: function (data) {
            return data.data;
        },

        /**
         * @method sync
         * @param method
         * @param collection
         * @param options
         */
        sync: function (method, collection, options) {
            var me = this;

            if ('read' === method) {
                SKApp.server.api('meeting/getSubjects', {}, function (data) {
                    options.success(data);
                });
            }
        }
    });

    return SKMeetingSubjectCollection;
});