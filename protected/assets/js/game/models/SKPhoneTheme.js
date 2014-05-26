/*global Backbone:false, console, SKApp, session */

define([], function () {
    "use strict";
    /**
     * @class SKPhoneTheme
     * @augments Backbone.Model
     */
    window.SKPhoneTheme = Backbone.Model.extend({

        /**
         * number .themeId
         * number .contactId, (php: Character.id)
         * string .themeTitle
         */

        /** @var string */
        idAttribute: 'themeId'
    });
    return window.SKPhoneTheme;
});