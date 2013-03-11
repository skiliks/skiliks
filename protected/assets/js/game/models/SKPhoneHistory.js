/*global Backbone:false, console, SKApp, session */

(function () {
    "use strict";
    /**
     * @class SKPhoneHistory
     * @constructor initialize
     */
    window.SKPhoneHistory = Backbone.Model.extend({
        defaults: {
            is_read:false
        }
    });
})();