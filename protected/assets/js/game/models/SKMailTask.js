/*
 *
 */
var SKMailTask;
(function() {
    "use strict";
    /**
     * @class SKMailTask
     * @augments Backbone.Model
     */
    SKMailTask = Backbone.Model.extend({
        // @var number
        mySqlId : undefined,

        // @var string, 
        text: undefined,
        
        // @var string
        duration: undefined, 
        
        // @var string, to provide easy I18N
        minuteslabel: 'мин',

        /**
         * @returns {string}
         */
        getFormatedDuration: function() {
            try {
                return this.duration + ' ' + this.minuteslabel;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
})();
