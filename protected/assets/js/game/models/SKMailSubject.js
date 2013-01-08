/* 
 * 
 */
(function() {
    "use strict";
    window.SKMailSubject = Backbone.Model.extend({
        // @var string
        code : undefined,
        
        // @var integer
        id : undefined,
        
        // @var integer
        characterSubjectId : undefined,

        // @var string, 
        text: undefined,
        
        getText: function() {
            return this.text;
        }
    });
})();
