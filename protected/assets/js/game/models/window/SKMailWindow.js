/**
 * Mail window subclass
 * @type {SKWindow}
 */
define(["game/models/window/SKWindow"],function () {
    "use strict";
    /**
     * @class SKMailWindow
     * @augments SKWindow
     */
    window.SKMailWindow = window.SKWindow.extend({
        /**
         * Constructor
         * @method initialize
         * @param subname
         * @param mailId
         */
        'initialize':function (subname, mailId) {
            window.SKWindow.prototype.initialize.call(this, 'mailEmulator', subname);
            this.set('params', {'mailId':mailId});
        },

        /**
         * Deactivates old window and activates new
         * @method switchMessage
         * @param mailId int message identifier
         */
        'switchMessage':function (mailId) {
            this.deactivate({silent:true});
            this.set('params', {'mailId':mailId});
            this.activate({silent:true});
        },

        /**
         * Sets plan element ID
         *
         * @method setPlan
         * @param planId
         */
        'setPlan': function (planId) {
            var params = this.get('params') || {};
            params.planId = planId;
            this.set('params', params);
        },

        /**
         * @method setMessage
         * @param mailId
         */
        'setMessage':function (mailId) {
            if (this.get('params') && this.get('params').mailId) {
                throw 'You can not set param mailId on this window, use switchMessage method';
            }
            this.set('params', {'mailId':mailId});
        }
    });
})();