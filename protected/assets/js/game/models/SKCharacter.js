/* 
 * 
 */
define([], function() {
    "use strict";
    /**
     * @class SKCharacter
     * @augments Backbone.Model
     */
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
         * @method
         * @return string
         */
        getFormatedForMailToName: function() {
            return this.name;
        },
        
        /**
         *
         * @todo rename
         * @method
         * @return string
         */
        getFormated_2_ForMailToName: function() {
            return this.name + ' <' + this.email + '>, ';
        }
    });
});