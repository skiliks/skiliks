/*global Backbone:false, console, SKServer */

(function () {
    "use strict";
    window.SKApplication = Backbone.Model.extend({
        'server': new SKServer()
    });

    window.SKApp = new window.SKApplication();
})();