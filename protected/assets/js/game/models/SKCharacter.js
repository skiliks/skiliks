/* 
 * 
 */
(function() {
    "use strict";
    window.SKCharacter = Backbone.Model.extend({
        // @var integer, MySQL id
        'mySQlId': undefined,
        
        // @var integer, ExcelId
        'excelId': undefined,

        // @var string, 
        'name': undefined,

        // @var string, 
        'email': undefined,

        // @var string, 
        'phoneNo': undefined,
        
        /**
         * @return string
         */
        'getFormatedForMailToName': function() {
            return this.name + ' &lt;' + this.email + '&gt;';
        }
    });
})();