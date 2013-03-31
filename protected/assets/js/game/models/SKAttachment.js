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
         * PHP myDocument
         * @property document
         * @type string
         * @default undefined
         */
        // document: undefined,

        /**
         * @property fileMySqlId
         * @type integer
         * @default undefined
         */
        fileMySqlId: undefined,

        /**
         * @method getFileName
         * @return string
         */
        getFileName: function() {
            // this is temporary version
            // when myDocument will be ready
            // code must det titles from this objects
            return this.title;
        },

        /**
         * @method getIconImagePath
         * @returns {string}
         */
        getIconImagePath: function() {
            if (0 < this.label.indexOf('.xls')) {
                return SKApp.get('assetsUrl') + "/img/documents/xls.png";
            }
            if (0 < this.label.indexOf('.doc')) {
                return SKApp.get('assetsUrl') + "/img/documents/doc.png";
            }
            if (0 < this.label.indexOf('.ppt')) {
                return SKApp.get('assetsUrl') + "/img/documents/ppt.png";
            }
            
            return '';
        }
    });
    return SKAttachment;
});
