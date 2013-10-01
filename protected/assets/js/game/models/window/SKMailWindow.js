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
            try {
                window.SKWindow.prototype.initialize.call(this, 'mailEmulator', subname);
                this.set('params', {'mailId':mailId});
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Deactivates old window and activates new
         * @method switchMessage
         * @param mailId int message identifier
         */
        'switchMessage':function (mailId) {
            try {
                this.deactivate({silent:true});
                this.set('params', {'mailId':mailId});
                this.activate({silent:true});
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Sets plan element ID
         *
         * @method setPlan
         * @param planId
         */
        'setPlan': function (planId) {
            try {
                var params = this.get('params') || {};
                params.planId = planId;
                this.set('params', params);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method setMessage
         * @param mailId
         */
        'setMessage':function (mailId) {
            try {
                if (this.get('params') && this.get('params').mailId) {
                    throw new Error ('You can not set param mailId on this window, use switchMessage method');
                }
                this.set('params', {'mailId':mailId});
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
})();