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
         * @param mySqlId
         * @returns {SkEmail}
         */
        getEmailByMySqlId: function(mySqlId) {
            try {
                mySqlId = parseInt(mySqlId, 10);
                var res_email = [];
                this.emails.forEach(function (email) {
                    if ('undefined' !== typeof email && parseInt(email.mySqlId, 10) === mySqlId) {
                        res_email = email;
                    }
                });
                return res_email;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @param oldId
         * @param newId
         * @returns {boolean}
         */
        updateEmailMySqlId: function(oldId, newId) {
            try {
                var result = false;
                this.emails.forEach(function (email) {
                    try {
                        if (parseInt(email.mySqlId, 10) === oldId) {
                            email.mySqlId = newId;

                            result = true;
                        }
                    } catch(exception) {
                        if (window.Raven) {
                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                        }
                    }
                });

                return result;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @returns {SkEmail}
         */
        getFirstEmail: function() {
            try {
                return this.emails[0];
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @returns {number}
         */
        countUnreaded: function() {
            try {
                var result = 0;

                for (var i in this.emails) {
                    if (false === this.emails[i].isRead()) {
                        result++;
                    }
                }

                return result;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return window.SKMailFolder;
});
