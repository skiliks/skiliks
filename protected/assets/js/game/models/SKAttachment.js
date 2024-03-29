/* 
 * 
 */
var SKAttachment;
define([], function() {
    "use strict";
    /**
     * @class SKAttachment
     * @augments Backbone.Model
     */
    SKAttachment = Backbone.Model.extend({
        /**
         * @property code
         * @type string, 'D1', 'D2'
         * @default undefined
         */
        code: undefined,

        /**
         * @property label
         * @type string
         * @default undefined
         */
        label: undefined,

        /**
         * @property fileMySqlId
         * @type number
         * @default undefined
         */
        fileMySqlId: undefined,

        /**
         * this is temporary version
         * when myDocument will be ready
         * code must det titles from this objects
         *
         * @method getFileName
         * @return string
         */
        getFileName: function() {
            return this.title;
        },

        /**
         * @method getIconImagePath
         * @returns {string}
         */
        getIconImagePath: function() {
            try {
                if (0 < this.label.indexOf('.xls')) {
                    return SKApp.get('assetsUrl') + "/img/documents/xls.png";
                }
                if (0 < this.label.indexOf('.doc')) {
                    return SKApp.get('assetsUrl') + "/img/documents/doc.png";
                }
                if (0 < this.label.indexOf('.ppt')) {
                    return SKApp.get('assetsUrl') + "/img/documents/ppt.png";
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
            
            return '';
        }
    });
    return SKAttachment;
});
