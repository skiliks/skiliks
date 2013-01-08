/* 
 * 
 */
(function() {
    "use strict";
    window.SKAttachment = Backbone.Model.extend({
        // @var string, 'D1', 'D2'
        code: undefined,

        // @var string,
        label: undefined,

        // @var excelDocument | wordDocument | poverPointDocument
        document: undefined,
        
        // @var integer
        fileMySqlId: undefined,

        /**
         * @return string
         */
        getFileName: function() {
            // this is temporari version
            // when excelDocument | wordDocument | poverPointDocument will be ready 
            // code must det titles from this objects
            return this.title;
        },
        
        getIconImagePath: function() {
            console.log(this.label.indexOf('.xls'));
            console.log(this.label.indexOf('.doc'));
            console.log(this.label.indexOf('.ppt'));
            if (0 < this.label.indexOf('.xls')) {
                return "../protected/assets/img/documents/xls.png";
            }
            if (0 < this.label.indexOf('.doc')) {
                return "../protected/assets/img/documents/doc.png";
            }
            if (0 < this.label.indexOf('.ppt')) {
                return "../protected/assets/img/documents/ppt.png";
            }
            
            return '';
        }
    });
})();
