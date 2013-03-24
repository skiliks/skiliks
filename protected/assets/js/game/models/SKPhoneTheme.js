/*global Backbone:false, console, SKApp, session */

define([], function () {
    "use strict";
    /**
     * @class SKPhoneTheme
     * @augments Backbone.Model
     */
    window.SKPhoneTheme = Backbone.Model.extend({
        idAttribute: 'themeId'
    });
    return window.SKPhoneTheme;
});