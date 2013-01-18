/*global Backbone:false, console, SKApp, session, SKPhoneTheme */

(function () {
    "use strict";
    window.SKPhoneThemeCollection = Backbone.Collection.extend({
        
        model:       SKPhoneTheme,
        characterId: undefined,
        
        initialize:function(options) {
            this.characterId = options.characterId;
        },
        parse:function(data) {
            return data.data;
        },
        sync:function (method, collection, options) {
            var phoneCollection = this;
            
            if ('read' === method) {
                SKApp.server.api(
                    'phone/getThemes', 
                    {id: phoneCollection.characterId }
                    ,function (data) { options.success(data); }
                );
            }
        }
    });
})();