/* 
 * 
 */
(function() {
    "use strict";
    /**
     * @class SKMailPhrase
     * @augments Backbone.Model
     */
    window.SKMailPhrase = Backbone.Model.extend({
        // @var integer
        mySqlId : undefined,

        // @var string, 
        text: undefined,
        
        // @var string
        // uniqueId - to identify prases with same text in email, when user want to delete one from such phrases
        uid: undefined,

        /**
         * Constructor
         * @method initialize
         */
        initialize: function() {
            this.uid = (new Date()).getTime() + '_' + Math.floor(Math.random()*100000);
        }
    });
})();
