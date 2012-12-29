/**
 * Documents window subclass
 * @type {SKWindow}
 */
(function () {
    "use strict";
    window.SKDialogWindow = window.SKWindow.extend({
        'initialize':function (name, subname, dialogId) {
            window.SKWindow.prototype.initialize.call(this, name, subname);
            this.set('params', {'dialogId': dialogId, 'lastDialogId':dialogId});
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