
define(["game/models/window/SKWindow"],function (SKWindow) {
    "use strict";
    /**
     * @class SKDocumentsWindow
     * @augments SKWindow
     */
    window.SKDocumentsWindow = SKWindow.extend({
        /**
         * @property single
         * @type boolean
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
            try {
                this.set('name', 'documents');
                this.set('id', this.get('subname') + ':' + this.get('fileId'));
                SKWindow.prototype.initialize.call(this, {name:'documents', subname:this.get('subname')});
                this.set('params', {'fileId':this.get('fileId')});
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        /**
         * Deactivates old window and activates new
         *
         * @method switchFile
         * @param fileId int file identifier
         * @return void
         */
        'switchFile':function (fileId) {
            try {
                this.deactivate({silent:true});
                this.set('params', {'fileId':fileId});
                this.activate({silent:true});
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method setFile
         * @param fileId
         * @return void
         */
        'setFile':function (fileId) {
            try {
                if (this.get('params') && this.get('params').fileId) {
                    throw new Error('You can not set param fileId on this window, use switchMessage method');
                }
                this.set('params', {'fileId':fileId});
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
    return window.SKDocumentsWindow;
});