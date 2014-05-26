/* 
 * 
 */
define([], function() {
    "use strict";
    /**
     * @class SKMailSubject
     * @augments Backbone.Model
     */
    window.SKMailSubject = Backbone.Model.extend({
        // @var string
        code : undefined,
        
        // @var number
        id : undefined,
        
        // @var number
        mySqlId : undefined,
        
        // @var number
        parentMySqlId : undefined,

        // @var string, 
        text: undefined,

        // @var string,
        themeId: undefined,

        /**
         * @returns {string}
         */
        getText: function() {
            return this.text;
        }
    });
    return window.SKMailSubject;
});
