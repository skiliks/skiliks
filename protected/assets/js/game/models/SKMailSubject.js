/* 
 * 
 */
(function() {
    "use strict";
    /**
     * @class SKMailSubject
     * @augments Backbone.Model
     */
    window.SKMailSubject = Backbone.Model.extend({
        // @var string
        code : undefined,
        
        // @var integer
        id : undefined,
        
        // @var integer
        mySqlId : undefined,
        
        // @var integer
        parentMySqlId : undefined,
        
        // @var integer
        characterSubjectId : undefined,

        // @var string, 
        text: undefined,

        /**
         * @method
         * @returns {*}
         */
        getText: function() {
            return this.text;
        }
    });
})();
