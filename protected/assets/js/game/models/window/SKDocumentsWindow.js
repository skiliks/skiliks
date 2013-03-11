/**
 * Documents window subclass
 * @class SKDocumentsWindow
 * @constructs
 * @type {SKWindow}
 */
define(["game/models/window/SKWindow"],function (SKWindow) {
    "use strict";
    /**
     * @class SKDocumentsWindow
     * @constructor initialize
     */
    window.SKDocumentsWindow = SKWindow.extend({
        /**
         * @property single
         * @type booloean
         * @default false
         */
        single: false,

        /**
         * Constructor
         *
         * @method initialize
         * @return void
         */
        'initialize':function () {
            this.set('name', 'documents');
            this.set('id', this.get('subname') + ':' + this.get('fileId'));
            SKWindow.prototype.initialize.call(this, {name:'documents', subname:this.get('subname')});
            this.set('params', {'fileId':this.get('fileId')});
        },
        /**
         * Deactivates old window and activates new
         *
         * @method switchFile
         * @param fileId int file identifier
         * @return void
         */
        'switchFile':function (fileId) {
            this.deactivate({silent:true});
            this.set('params', {'fileId':fileId});
            this.activate({silent:true});
        },

        /**
         * @method setFile
         * @param fileId
         * @return void
         */
        'setFile':function (fileId) {
            if (this.get('params') && this.get('params').fileId) {
                throw 'You can not set param fileId on this window, use switchMessage method';
            }
            this.set('params', {'fileId':fileId});
        }
    });
});