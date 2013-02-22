/* 
 * 
 */
define([], function() {
    "use strict";
    window.SKCharacter = Backbone.Model.extend({
        // @var integer, MySQL id
        mySqlId: undefined,
        
        // @var integer, ExcelId
        excelId: undefined,

        // @var string, 
        name: undefined,

        // @var string, 
        email: undefined,

        // @var string, 
        phoneNo: undefined,
        
        /**
         * @return string
         */
        getFormatedForMailToName: function() {
            return this.name + ', ' + this.email + ' (' + this.mySqlId + ')';
        },
        
        /* 
         * Sorry,I can`t find good name for this function
         * @return string
         */
        getFormated_2_ForMailToName: function() {
            return this.name + ' <' + this.email + '>, ';
        }
    });
});