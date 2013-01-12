/**
 * Documents window subclass
 * @type {SKWindow}
 */
(function () {
    "use strict";
    window.SKDialogWindow = window.SKWindow.extend({
        'initialize':function () {
            window.SKWindow.prototype.initialize.call(this, this.get('name'), this.get('subname'));
            this.set('params', {'dialogId': this.get('dialog_id'), 'lastDialogId':this.get('dialog_id')});
        },

        /**
         * Deactivates old window and activates new
         * @param dialogId
         */
        'switchDialog':function (dialogId) {
            if (!this.is_opened) {
                throw "Window is already closed";
            }
            this.deactivate();
            this.set('params', {'dialogId': dialogId});
            this.activate();
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