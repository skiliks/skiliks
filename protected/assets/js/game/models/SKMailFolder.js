/*global Backbone
 */
(function() {
    "use strict";
    window.SKMailFolder = Backbone.Model.extend({
        // @var string

        alias: undefined,

        // @var string, 
        name: undefined,
        
        // @var array of 
        emails: [],
       
        // @bar bool
        isActive: false,
        
        addEmail: function(email) {
            this.emails.push(email);
        },
        
        getEmailByMySqlId: function(mySqlId) {
            mySqlId = parseInt(mySqlId, 10);
            var res_email;
            this.emails.forEach(function (email) {
                if ('undefined' !== typeof email && parseInt(email.mySqlId, 10) === mySqlId) {
                    res_email = email;
                }
            });
            return res_email;
        },
        
        updateEmailMySqlId: function(oldId, newId) {
            this.emails.forEach(function (email) {
                if (parseInt(email.mySqlId, 10) === oldId) {
                    email.mySqlId = newId;
                    
                    return true;
                }
            }); 
            
            return false;
        },
        
        getFirstEmail: function() {
            for (var i in this.emails) {
                return this.emails[i];
            }
            
            return undefined;
        }
    });
})();
