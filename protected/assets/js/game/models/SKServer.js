/**
 * Взаимодействие с сервером через API. Запросы посылаются POST-ом, в каждый добавляется параметр sid
 *
 * Поддерживается xdebug в ajax-запросах
 *
 * @class SKServer
 * @constructor
 */
define(["jquery/jquery.cookies"], function () {
    "use strict";
    var SKServer = Backbone.Model.extend(
        /**
         * @lends SKServer.prototype
         */
        {
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
                    console.warn('Use of sync ajax request for '+ url +' is very bad practice. Will remove this behavior');
                }
                if (debug_match !== null) {
                    url += '?XDEBUG_SESSION_START=' + debug_match[1];
                }
                if ($.cookie('sid')) {
                    params.sid = $.cookie('sid');
                }
                var result = $.ajax({
                    data:params,
                    url:url,
                    type:"POST",
                    dataType:"json",
                    async:async,
                    success:function (data) {
                        result = data;
                        if (typeof cb !== 'undefined') {
                            cb(data);
                        }
                    },
                    error:function (jqXHR, textStatus, errorThrown) {
                        console.log(url + ' error ' + errorThrown);
                        /**
                         * Вызывается, если сервер вернул не 200-й статус
                         *
                         * @event server:error
                         */
                        me.trigger('server:error');
                    }
                });
                return result;
            }
        });
    return SKServer;
});