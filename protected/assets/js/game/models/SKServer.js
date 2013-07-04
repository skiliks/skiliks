/*global _, Backbone, define, $, SKApp, console*/
define([
    "jquery/jquery.cookies",
    "jquery/ajaxq",
    'game/models/SKRequestsQueue'], function (cookies, ajaxq, SKRequestsQueue) {
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
            api_root: '/index.php/',

            requests_queue:null,

            onError: function (xhr, ajaxOptions, thrownError) {
                /**
                 * Сообщение об ошибке
                 *
                 * @event server:error
                 */
                // console.log(xhr, ajaxOptions, thrownError);
                if (SKApp.get('isDisplayServer500errors')) {
                    this.trigger('server:error');
                }
            },

            onComplete: function (xhr, text_status) {
                //console.log(xhr.status);
                if ('timeout' === text_status || xhr.status === 0) {
                    //console.log(xhr, text_status);
                    SKApp.isInternetConnectionBreakHappent = true;
                    if($('.time').hasClass('paused')) {
                        throw new Error("Симуляция ");
                    }else{
                        $('.time').addClass('paused');
                        SKApp.simulation.startPause();
                    }
                }
            },

            getAjaxParams: function (path, params, callback) {
                var me = this;
                var debug_match = location.search.match(/XDEBUG_SESSION_START=(\d+)/),
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
                params.uniqueId = _.uniqueId('request');
                params.time = SKApp.simulation.getGameTime();
                return {
                    data:      params,
                    url:       url,
                    type:      "POST",
                    dataType:  "json",
                    xhrFields: {
                        withCredentials: true
                    },
                    timeout: parseInt(SKApp.get('frontendAjaxTimeout')),
                    beforeSend: function(jqXHR, settings) {
                        //console.log(settings);
                        var uniqueId = SKApp.server.getQueryStringParams(settings.data, 'uniqueId');
                        if( undefined !== uniqueId ) {
                            //var model = new SKRequestsQueue();
                            //model.uniqueId = uniqueId;
                            //console.log(model);
                            //console.log(jqXHR);
                            if($.isEmptyObject(SKApp.server.requests_queue.where({uniqueId:uniqueId}))){
                                SKApp.server.requests_queue.add(new SKRequestsQueue({uniqueId:uniqueId, url:url}));
                            } else {
                                throw new Error("Duplicate uniqueId - "+uniqueId);
                            }
                            //console.log(SKApp.server.requests_queue);
                        } else {
                            throw new Error("uniqueId is not found");
                        }
                    },
                    success:  function (data, textStatus, jqXHR) {
                        console.log(url);
                        if( data.uniqueId !== undefined ) {
                            var models = SKApp.server.requests_queue.where({uniqueId:data.uniqueId});
                            if(false === $.isEmptyObject(models)) {
                                //console.log(SKApp.server.requests_queue);
                                SKApp.server.requests_queue.remove(models[0]);
                            } else {
                                if (!window.testMode) {
                                    throw new Error("Not found model by - " + uniqueId);
                                }
                            }
                            if(undefined !== callback){
                                callback(data, textStatus, jqXHR);
                            }
                        } else {
                            if (!window.testMode) {
                                throw new Error("uniqueId is not found");
                            }
                        }
                    },
                    complete: _.bind(me.onComplete, me),
                    error: _.bind(me.onError, me)
                };
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
            api:function (path, params, callback) {

                var ajaxParams = this.getAjaxParams(path, params, callback);
                return $.ajax(ajaxParams);
            },
            /**
             * Отправляет запрос на сервер или ставит его в очередь в случае, если такой запрос уже выполняется
             * @method apiQueue
             */
            apiQueue: function (queue, path, params, callback) {
                var ajaxParams = this.getAjaxParams(path, params, callback);
                return $.ajaxq(queue, ajaxParams);
            },
            getQueryStringParams: function(sPageURL, sParam) {
                var sURLVariables = sPageURL.split('&');
                for (var i = 0; i < sURLVariables.length; i++)
                {
                    var sParameterName = sURLVariables[i].split('=');
                    if (sParameterName[0] == sParam)
                    {
                        return sParameterName[1];
                    }
                }
                return undefined;
            }
        });
    return SKServer;
});