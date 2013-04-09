
define(["jquery/jquery.cookies", "jquery/ajaxq"], function () {
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
            'getAjaxParams': function (path, params, callback) {
                var me = this;
                var cb = callback,
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
                return {
                    data:      params,
                    url:       url,
                    type:      "POST",
                    dataType:  "json",
                    async:     async,
                    xhrFields: {
                        withCredentials: true
                    },
                    'success': function (data) {
                        if (typeof cb !== 'undefined') {
                            cb(data);
                        }
                    },
                    'error':   function (jqXHR, textStatus, errorThrown) {
                        //console.log(url + ' error ' + errorThrown);
                        /**
                         * Сообщение об ошибке
                         *
                         * @event server:error
                         */
                        me.trigger('server:error');
                    }
                }
            },
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

                var ajaxParams = this.getAjaxParams(path, params, callback);
                return $.ajax(ajaxParams);
            },
            /**
             * Отправляет запрос на сервер или ставит его в очередь в случае, если такой запрос уже выполняется
             * @method apiQueue
             */
            'apiQueue': function (queue, path, params, callback) {
                var ajaxParams = this.getAjaxParams(path, params, callback);
                return $.ajaxq(queue, ajaxParams);
            }
        });
    return SKServer;
});