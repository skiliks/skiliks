/* 
 * 
 */
(function() {
    "use strict";
    window.SKAttachment = Backbone.Model.extend({
        // @var string, 'D1', 'D2'
        'code': undefined,

        // @var string, 

        'label': undefined,

        // @var excelDocument | wordDocument | poverPointDocument
        'document': undefined,

        /**
         * @return string
         */
        'getFileName': function() {
            // this is temporari version
            // when excelDocument | wordDocument | poverPointDocument will be ready 
            // code must det titles from this objects
            return this.title;
        }
    });
})();
