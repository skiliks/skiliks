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

        /**
         * @property mySqlId
         * @type number
         * @default undefined
         */
        mySqlId: undefined,

        /**
         * @property excelId
         * @type number
         * @default undefined
         */
        excelId: undefined,

        /**
         * @property name
         * @type string
         * @default undefined
         */
        name: undefined,

        /**
         * @property email
         * @type string
         * @default undefined
         */
        email: undefined,

        /**
         * @method getFormatedForMailToName
         * @return string
         */
        getFormatedForMailToName: function() {
            try {
                return this.get('fio') + ' (' + this.get('title') + ')';
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        
        /**
         * @method getFormattedRecipientLabel
         * @return string
         */
        getFormattedRecipientLabel: function() {
            try {
                return this.get('fio') + ' <' + this.get('email') + '>, ';
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
    return window.SKCharacter;
});