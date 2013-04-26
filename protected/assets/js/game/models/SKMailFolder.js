/*global Backbone, define
 */
define([], function() {
    "use strict";
    /**
     * @class SKMailFolder
     * @augments Backbone.Model
     */
    window.SKMailFolder = Backbone.Model.extend({
        // @var string

        alias: undefined,

        // @var string, 
        name: undefined,
        
        // @var array of 
        emails: [],
       
        // @bar bool
        isActive: false,

        /**
         *
         * @param email
         */
        addEmail: function(email) {
            this.emails.push(email);
        },

        /**
         * @method
         * @param mySqlId
         * @returns {*}
         */
        getEmailByMySqlId: function(mySqlId) {
            mySqlId = parseInt(mySqlId, 10);
            var res_email = [];
            this.emails.forEach(function (email) {
                if ('undefined' !== typeof email && parseInt(email.mySqlId, 10) === mySqlId) {
                    res_email = email;
                }
            });
            return res_email;
        },

        /**
         * @method
         * @param oldId
         * @param newId
         * @returns {boolean}
         */
        updateEmailMySqlId: function(oldId, newId) {
            var result = false;
            this.emails.forEach(function (email) {
                if (parseInt(email.mySqlId, 10) === oldId) {
                    email.mySqlId = newId;
                    
                    result = true;
                }
            }); 
            
            return result;
        },

        /**
         * @method
         * @returns {*}
         */
        getFirstEmail: function() {
            return this.emails[0];
        },

        /**
         * @method
         * @returns {number}
         */
        countUnreaded: function() {
            var result = 0;
            
            for (var i in this.emails) {
                if (false === this.emails[i].isRead()) {
                    result++;
                }
            }
            
            return result;
        }
    });

    return window.SKMailFolder;
});
