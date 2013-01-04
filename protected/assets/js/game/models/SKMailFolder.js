/* 
 * 
 */
(function() {
    "use strict";
    window.SKMailFolder = Backbone.Model.extend({
        // @var string

        alias: undefined,

        // @var string, 
        name: undefined,
        
        // @var array of 
        emails: new Array(),
       
        // @bar bool
        isActive: false,
        
        addEmail: function(email) {
            this.emails.push(email);
        },
        
        getEmailByMySqlId: function(mySqlId) {
            mySqlId = parseInt(mySqlId);
            for (var id in this.emails) {
                if ('undefined' != typeof this.emails[id] && this.emails[id].mySqlId == mySqlId) {
                    return this.emails[id];
                }
            }
        }
    });
})();
