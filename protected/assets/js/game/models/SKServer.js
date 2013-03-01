/**
 * Взаимодействие с сервером через API
 *
 * @class SKServer
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
             */
            'api_root': '/index.php/',
            /**
             * Отправляет запрос на сервер
             * @method api
             * @param {String} path
             * @param {Object|undefined} params
             * @param {function(data:Object)|undefined} callback
             * @return {$.xhr}
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
                        me.message_window = me.message_window || new window.SKDialogView({
                            'message':'Увы, произошла ошибка! Нам очень жаль и мы постараемся исправить ее как можно скорее',
                            'buttons':[
                                {
                                    'value':'Окей',
                                    'onclick':function () {
                                        delete me.message_window;
                                    }
                                }
                            ]
                        });
                    }
                });
                return result;
            }
        });
    return SKServer;
});