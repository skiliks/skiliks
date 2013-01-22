/**
 * Documents window subclass
 * @type {SKWindow}
 */
(function () {
    "use strict";
    window.SKDialogWindow = window.SKWindow.extend({
        'initialize':function () {
            window.SKWindow.prototype.initialize.call(this);
            this.set('code', this.get('sim_event').get('data')[0].code);
            this.set('params', {
                'dialogId': this.get('sim_event').get('data')[0].id,
                'lastDialogId':this.get('sim_event').get('data')[0].id});
        },

        /**
         * Deactivates old window and activates new
         * @param dialogId
         */
        'switchDialog':function (dialogId) {
            if (!this.is_opened) {
                throw "Window is already closed";
            }
            this.deactivate({silent:true});
            this.set('params', {'dialogId': dialogId});
            this.activate({silent:true});
        },
        'setDialog':function (dialogId) {
            if (this.get('params') && this.get('params').dialogId) {
                throw 'You can not set param dialogId on this window, use switchMessage method';
            }
            this.set('params', {'dialogId': dialogId});
        },
        'setLastDialog':function (dialogId) {
            this.get('params').lastDialogId = dialogId;
        }
    });
})();