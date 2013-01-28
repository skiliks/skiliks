/*global Backbone:false, console, session, SKDialogView */

/**
 * @class
 */
var SKServer;
(function () {
    "use strict";
    SKServer = Backbone.Model.extend(
        /**
         * @lends SKServer.prototype
         */
        {
            /**
             * @private
             */
            'api_root':'/index.php/',
            /**
             *
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
                    console.warn('Use of sync ajax request is very bad practice. Will remove this behavior');
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
                    error:function () {
                        me.message_window = me.message_window || new SKDialogView({
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

})();