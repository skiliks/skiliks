/*global Backbone:false, console, SKApp */

define([], function () {
    "use strict";
    /**
     * @class SKPhoneHistory
     * @augments Backbone.Model
     */
    window.SKPhoneHistory = Backbone.Model.extend({

        /**
         * number .type, php class: PhoneCall, constants
         * string .name, recipient name
         * string .date, call date-time
         * string .dialog_code, like 'RVT1'
         */

        defaults: {
            is_displayed:false
        }
    });
    return window.SKPhoneHistory;
});