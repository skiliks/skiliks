/**
 * Documents window subclass
 * @type {SKWindow}
 */
define(["game/models/window/SKWindow"], function (SKWindow) {
    "use strict";
    /**
     * @class SKDialogWindow
     * @augments SKWindow
     */
    window.SKDialogWindow = SKWindow.extend({

        /**
         * Constructor
         * @method initialize
         * @return void
         */
        'initialize': function () {
            try {
                window.SKWindow.prototype.initialize.call(this);
                this.set('code', this.get('sim_event').get('data')[0].code);
                this.set('params', {
                    'dialogId': this.get('sim_event').get('data')[0].id,
                    'lastDialogId': this.get('sim_event').get('data')[0].id});
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Deactivates old window and activates new
         *
         * @method switchDialog
         * @param dialogId
         * @return void
         */
        'switchDialog': function (dialogId) {
            try {
                if (!this.is_opened) {
                    throw new Error ("Window is already closed");
                }
                this.deactivate({silent: true});
                this.set('params', {'dialogId': dialogId});
                this.activate({silent: true});
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method setDialog
         * @param dialogId
         * @return void
         */
        'setDialog': function (dialogId) {
            try {
                if (this.get('params') && this.get('params').dialogId) {
                    throw new Error ('You can not set param dialogId on this window, use switchMessage method');
                }
                this.set('params', {'dialogId': dialogId});
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method setLastDialog
         * @param dialogId
         * @return void
         */
        'setLastDialog': function (dialogId) {
            try {
                this.get('params').lastDialogId = dialogId;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
    return window.SKDialogWindow;
});