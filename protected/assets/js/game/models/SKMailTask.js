/* 
 * 
 */
(function() {
    "use strict";
    window.SKMailTask = Backbone.Model.extend({
        // @var integer
        mySqlId : undefined,

        // @var string, 
        text: undefined,
        
        // @var string
        duration: undefined, 
        
        // @var string, to provide easy I18N
        minuteslabel: 'мин',
        
        initialize: function() { },
        
        getFormatedDuration: function() {
            return this.duration + ' ' + this.minuteslabel;
        }
    });
})();
