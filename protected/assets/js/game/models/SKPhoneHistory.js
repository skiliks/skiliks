/*global Backbone:false, console, SKApp */

define([], function () {
    "use strict";
    /**
     * @class SKPhoneHistory
     * @augments Backbone.Model
     */
    window.SKPhoneHistory = Backbone.Model.extend({
        defaults: {
            is_displayed:false
        }
    });
    return window.SKPhoneHistory;
});