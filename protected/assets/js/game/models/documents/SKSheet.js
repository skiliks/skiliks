/*global Backbone:false, console, SKApp, session */

define([], function () {
    "use strict";
    var SKSheet = Backbone.Model.extend({
        activate: function () {
            var me = this;
            this.collection.each(function (sheet) {
                if (sheet.id === me.id) {
                    me.trigger('activate');
                } else {
                    me.trigger('deactivate');
                }
            });
        }
    });
    return SKSheet;
});