/*global Backbone:false, console, session */

(function () {
    "use strict";
    window.SKServer = Backbone.Model.extend({
        'api_root': '/api/index.php/',
        'api': function (path, params, callback) {
            var cb = callback;
            var debug_match = location.search.match(/XDEBUG_SESSION_START=(\d+)/);
            var url = this.api_root + path;
            if (debug_match !== null) {
                url += '?XDEBUG_SESSION_START=' + debug_match[1];
            }
            if (session.getSid()) {
                params.sid = session.getSid();
            }
            var result;
            $.ajax({
                data:     params,
                url:      url,
                type:     "POST",
                dataType: "json",
                success: function (data){
                    result = data;
                    if (typeof cb !== 'undefined') {
                        cb(data);
                    }
                },
                error: function () {
                    window.alert("Увы, произошла ошибка! Нам очень жаль и мы постараемся исправить ее как можно скорее");
                }
            });
            return result;
        }
    });
})();