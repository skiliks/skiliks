/**
 * Documents window subclass
 * @type {SKWindow}
 */
(function () {
    "use strict";
    window.SKDocumentsWindow = window.SKWindow.extend({
        'initialize':function (subname, fileId) {
            window.SKWindow.prototype.initialize.call(this, 'documents', subname);
            this.set('params', {'fileId': fileId});
        },
        /**
         * Deactivates old window and activates new
         * @param fileId int file identifier
         */
        'switchFile':function (fileId) {
            this.deactivate();
            this.set('params', {'fileId': fileId});
            this.activate();
        },

        'setFile':function (fileId) {
            if (this.get('params') && this.get('params').fileId) {
                throw 'You can not set param fileId on this window, use switchMessage method';
            }
            this.set('params', {'fileId': fileId});
        }
    });
})();