/*global Backbone:false, console, SKApp, session */

define([], function () {
    "use strict";
    var SKSheet = Backbone.Model.extend({
        activate: function () {
            var me = this;
            this.collection.each(function (sheet) {
                if (sheet.get('name') === me.get('name')) {
                    sheet.set('active', true);
                    sheet.trigger('activate');
                } else {
                    sheet.set('active', false);
                    sheet.trigger('deactivate');
                }
            });
        }
    });
    return SKSheet;
});