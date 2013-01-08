/*global Backbone:false, console, SKApp, session */

(function () {
    "use strict";
    window.SKTodoTask = Backbone.Model.extend({
        sync: function (method) {
            if (method !== 'delete') {
                Backbone.Model.sync.apply(this, arguments);
            }
        }
    });
})();