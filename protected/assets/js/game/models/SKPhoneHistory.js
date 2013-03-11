/*global Backbone:false, console, SKApp, session */

(function () {
    "use strict";
    /**
     * @class SKPhoneHistory
     * @augments Backbone.Model
     */
    window.SKPhoneHistory = Backbone.Model.extend({
        defaults: {
            is_read:false
        }
    });
})();