/* 
 * 
 */
define([], function() {
    "use strict";
    /**
     * @class SKMailPhrase
     * @augments Backbone.Model
     */
    window.SKMailPhrase = Backbone.Model.extend({
        // @var number
        mySqlId : undefined,

        // @var string, 
        text: undefined,

        // @var string,
        columnNumber: undefined,
        
        // @var string
        // uniqueId - to identify prases with same text in email, when user want to delete one from such phrases
        uid: undefined,

        /**
         * Constructor
         */
        initialize: function() {
            try {
                this.uid = (new Date()).getTime() + '_' + Math.floor(Math.random()*100000);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
    return window.SKMailPhrase;
});
