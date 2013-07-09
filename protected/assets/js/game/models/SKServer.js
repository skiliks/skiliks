/*global _, Backbone, define, $, SKApp, console, SKDialogView*/
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

            connectPath:'simulation/Connect',

            requests_queue:null,

            request_interval_id:null,

            is_connected:true,

            try_connect:false,

            dialog_window:null,

            getAjaxParams: function (path, params, callback) {
                var me = this;
                var debug_match = location.search.match(/XDEBUG_SESSION_START=(\d+)/);
                var url;
                    if(path.length > this.api_root.length && path.indexOf(this.api_root) === 0){
                        url = path;
                    }else{
                        url = this.api_root + path;
                    }

                    var async = true;
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
                if(params.is_repeat_request){
                    params.request = 'repeat';
                }
                if(params.uniqueId === undefined) {
                    params.uniqueId = _.uniqueId('request');
                }else{
                    var models = SKApp.server.requests_queue.where({uniqueId:params.uniqueId, is_repeat_request:true});
                    if(!_.isEmpty(models)) {
                        params.request = 'repeat';
                    } else {
                        throw new Error(" uniqueId define but is not repeat request ");
                    }
                }
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
                        if( undefined !== params.uniqueId ) {

                            if( url !== me.api_root + me.connectPath ) {
                                var models = SKApp.server.requests_queue.where({uniqueId:params.uniqueId});
                                if($.isEmptyObject(models)){
                                    SKApp.server.requests_queue.add(new SKRequestsQueue({uniqueId:params.uniqueId, url:url, data:params, callback:callback, is_repeat_request:false}));
                                }else if(_.first(models).get('is_repeat_request')) {
                                    console.log("repeat"+_.first(models).get('uniqueId'));
                                } else {
                                    throw new Error("Duplicate uniqueId - "+params.uniqueId);
                                }
                            }
                            //console.log(SKApp.server.requests_queue);
                        } else {
                            throw new Error("uniqueId is not found");
                        }
                    },
                    success:  function (data, textStatus, jqXHR) {
                        //console.log(url);
                        if( data.uniqueId !== undefined ) {

                            if( url !== me.api_root + me.connectPath ) {
                                var models = SKApp.server.requests_queue.where({uniqueId:data.uniqueId});
                                if(false === $.isEmptyObject(models)) {
                                //console.log(SKApp.server.requests_queue);
                                    SKApp.server.requests_queue.remove(_.first(models));
                                } else {
                                    if (!window.testMode) {
                                        throw new Error("Not found model by - " + data.uniqueId);
                                    }
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
                    complete: function (xhr, text_status) {

                        if ('timeout' === text_status || xhr.status === 0) {

                            SKApp.isInternetConnectionBreakHappent = true;

                            if( url !== me.api_root + me.connectPath && me.try_connect === false) {
                                if($('.time').hasClass('paused')) {
                                    throw new Error("Симуляция ");
                                } else {
                                    me.dialog_window = new SKDialogView({
                                        'message': "Пропало Интернет соединение. Симуляция поставлена на паузу."+
                                        "Пожалуйста, проверьте Интернет соединение."+
                                        "Как только соединение восстановится, мы предложим вам продолжить симуляцию",
                                        'modal': true,
                                        'buttons': []
                                    });
                                    $('.time').addClass('paused');
                                    SKApp.simulation.startPause();
                                }
                                me.tryConnect();
                            }

                        } else if( xhr.status === 200 ) {
                            if( url === me.api_root + me.connectPath ) {
                                console.log("Connect");
                                me.stopTryConnect();
                                me.dialog_window.remove();
                                delete me.dialog_window;
                                me.dialog_window = new SKDialogView({
                                    'message': 'Соединение с интернет востановлено!',
                                    'modal': true,
                                    'buttons': [
                                        {
                                            'value': 'Продолжить игру',
                                            'onclick': function () {
                                                SKApp.simulation.updatePause(function(){
                                                    SKApp.simulation.stopPause(function() {
                                                        $('.time').removeClass('paused');
                                                        SKApp.server.requests_queue.each(function(request) {
                                                            request.set('is_repeat_request', true);
                                                            SKApp.server.api(request.get('url'), request.get('data'), request.get('callback'));
                                                        });
                                                        me.dialog_window.remove();
                                                        delete me.dialog_window;
                                                    });
                                                });
                                            }
                                        }
                                    ]
                                });

                            }
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {

                        if (SKApp.get('isDisplayServer500errors')) {
                            this.trigger('server:error');
                        }
                    }
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

            tryConnect: function() {
                this.try_connect = true;
                var me = this;
                this.request_interval_id = setInterval(function(){
                    me.api(me.connectPath, {}, function(){});
                }, 5000);
            },
            stopTryConnect: function() {
                this.try_connect = false;
                if(this.request_interval_id !== null){
                    clearInterval(this.request_interval_id);
                }
            }
        });
    return SKServer;
});