/*global Backbone:false, console, SKApp, session */

(function () {
    "use strict";
    window.SKTodoTask = Backbone.Model.extend({
        sync: function (method) {
            if (method !== 'delete' && method !== 'create') {
                Backbone.Model.prototype.sync.apply(this, arguments);
            }
        }
    });
})();