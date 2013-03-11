/* 
 * 
 */
var SKAttachment;
define([], function() {
    "use strict";
    /**
     * @class SKAttachment
     * @constructor initialize
     */
    SKAttachment = Backbone.Model.extend({
        // @var string, 'D1', 'D2'
        code: undefined,

        // @var string,
        label: undefined,

        // @var myDocument
        document: undefined,
        
        // @var integer
        fileMySqlId: undefined,

        /**
         * @return string
         */
        getFileName: function() {
            // this is temporary version
            // when myDocument will be ready
            // code must det titles from this objects
            return this.title;
        },
        
        getIconImagePath: function() {
            if (0 < this.label.indexOf('.xls')) {
                return SKConfig.assetsUrl + "/img/documents/xls.png";
            }
            if (0 < this.label.indexOf('.doc')) {
                return SKConfig.assetsUrl + "/img/documents/doc.png";
            }
            if (0 < this.label.indexOf('.ppt')) {
                return SKConfig.assetsUrl + "/img/documents/ppt.png";
            }
            
            return '';
        }
    });
    return SKAttachment;
});
