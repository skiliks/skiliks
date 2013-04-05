
define(["jquery/jquery.cookies"], function () {
    "use strict";
    /**
     * Взаимодействие с сервером через API. Запросы посылаются POST-ом, в каждый добавляется параметр sid
     *
     * Поддерживается xdebug в ajax-запросах
     *
     * @class SKServer
     * @augments Backbone.Model
     */
    var SKServer = Backbone.Model.extend({
            /**
             * @private
             * @property api_root
             */
            'api_root': '/index.php/',
            /**
             * Отправляет запрос на сервер
             * @example
             *     SKApp.server.api('todo/get', {}, function (data) {})
             * @method api
             * @param {String} path
             * @param {Object|undefined} params
             * @param {function(data:Object)|undefined} callback
             * @return {$.xhr}
             * @async
             */
            'api':function (path, params, callback) {

                var me = this,
                    cb = callback,
                    debug_match = location.search.match(/XDEBUG_SESSION_START=(\d+)/),
                    url = this.api_root + path,
                    async = true;
                if (params === undefined) {
                    params = {};
                }
                if (arguments.length === 4) {
                    async = arguments[3];
                }
                if (async === false) {
                    throw ('Use of sync ajax request for '+ url +' does not work anymore');
                }
                if (debug_match !== null) {
                    url += '?XDEBUG_SESSION_START=' + debug_match[1];
                }
                var ajaxParams = {
                    data:      params,
                    url:       url,
                    type:      "POST",
                    dataType:  "json",
                    async:     async,
                    xhrFields: {
                        withCredentials: true
                    }
                };
                _.extend(ajaxParams, {
                    'success':   function (data) {
                        result = data;
                        if (typeof cb !== 'undefined') {
                            cb(data);
                        }
                    },
                    'error':     function (jqXHR, textStatus, errorThrown) {
                        console.log(url + ' error ' + errorThrown);
                        /**
                         * Вызывается, если сервер вернул не 200-й статус
                         *
                         * @event server:error
                         */
                        me.trigger('server:error');
                    }
                });
                var result = $.ajax(ajaxParams);


                return result;
            }
        });
    return SKServer;
});