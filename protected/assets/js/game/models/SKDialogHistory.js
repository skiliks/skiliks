/*global Backbone, define
 */
define([], function() {

    "use strict";

    /**
     * @class SKDialogHistory
     * @augments Backbone.Model
     */
    window.SKDialogHistory = Backbone.Model.extend({

        /**
         * Constructor
         * @method initialize
         * @return void
         */
        /*initialize: function (options) {
            try {
                var me = this;

                me.set('dialog_code', options.dialog_code);
                me.set('replica_id',  options.replica_id);
                me.set('is_sent',     options.is_sent);
                me.set('type', null);  // 'phone', 'visit'
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }*/
    });

    return window.SKDialogHistory;
});
