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
         * @type integer
         * @default undefined
         */
        mySqlId: undefined,

        /**
         * @property excelId
         * @type integer
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
            return this.name;
        },
        
        /**
         * @method getFormattedRecipientLabel
         * @return string
         */
        getFormattedRecipientLabel: function() {
            return this.name + ' <' + this.email + '>, ';
        }
    });
    return window.SKCharacter;
});