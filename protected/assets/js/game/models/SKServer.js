/*global _, Backbone, define, $, SKApp, console, SKDialogView*/
define([
    "jquery/jquery.cookies",
    "jquery/jquery.ajaxQueue",
    'game/models/SKRequestsQueue'], function (cookies, ajaxq, SKRequestsQueue) {
    "use strict";
    /**
     * Взаимодействие с сервером через API. Запросы посылаются POST-ом, в каждый добавляется параметр sid
     *
     * Поддерживается xdebug в ajax-запросах
     *
     * @class SKServer
     * @augments Backbone.Model
     * @constructs
     */
    var SKServer = Backbone.Model.extend(
        /** @lends SKServer */
        {
            /**
             * @private
             * @property api_root
             */
            api_root: '/index.php/',

            connectPath:'simulation/Connect',

            requests_queue:null,

            last_200_request:[],

            //requests_tmp:[],

            request_interval_id:null,

            is_connected:true,

            is_active_game:true,

            try_connect:false,

            error_dialog:null,

            success_dialog:null,

            requests_timeout:10000,

            getAjaxParams: function (path, params, callback) {
                try {
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
                        throw new Error ('Use of sync ajax request for '+ url +' does not work anymore');
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
                        var repeat_requests = SKApp.server.requests_queue.where({uniqueId:params.uniqueId, is_repeat_request:true});
                        if(!_.isEmpty(repeat_requests)) {
                            params.request = 'repeat';
                        } else {
                            throw new Error(" uniqueId define but is not repeat request ");
                        }
                    }
                    if (SKApp.simulation !== undefined) {
                        params.simId = SKApp.simulation.id;
                    }
                    params.time = SKApp.simulation.getGameTime({with_seconds:true});
                    if( url !== me.api_root + me.connectPath ) {
                        var models = SKApp.server.requests_queue.where({uniqueId:params.uniqueId});
                        if($.isEmptyObject(models)){
                            SKApp.server.requests_queue.add(new SKRequestsQueue({
                                uniqueId:params.uniqueId,
                                url:url, data:params,
                                callback:callback,
                                is_repeat_request:false,
                                ajax:null,
                                status:'pending'
                            }));
                        } else if(_.first(models).get('is_repeat_request')) {

                        } else {
                            throw new Error("Duplicate uniqueId - "+params.uniqueId);
                        }
                    }
                    return {
                        data:       params,
                        url:        url,
                        type:       "POST",
                        dataType:   "json",
                        xhrFields: {
                            withCredentials: true
                        },
                        timeout: parseInt(me.requests_timeout),
                        success:  function (data, textStatus, jqXHR) {

                            if(typeof data.redirect === 'string') {
                                $(window).off('beforeunload');
                                location.assign('/dashboard');
                            } else {
                                if( data.uniqueId !== undefined ) {

                                    if( url !== me.api_root + me.connectPath ) {
                                        var models = SKApp.server.requests_queue.where({uniqueId:data.uniqueId});
                                        if(false === $.isEmptyObject(models)) {
                                            SKApp.server.requests_queue.remove(_.first(models));
                                        } else {
                                            if (!window.testMode) {
                                                throw new Error("Not found model by - " + data.uniqueId);
                                            }
                                        }
                                    }
                                    if(undefined !== callback){

                                        if(data.simulation_status !== 'interrupted'){
                                            //if(me.last_200_request.push(data.uniqueId)) //me.last_200_request.push(data.uniqueId);
                                            if(me.isRunCallBack(data.uniqueId)) {
                                                callback(data, textStatus, jqXHR);
                                            }
                                        }else{
                                            $(window).off('beforeunload');
                                            location.assign('/simulation/exit');
                                        }
                                    } else {
                                        if(data.simulation_status == 'interrupted') {
                                            $(window).off('beforeunload');
                                            location.assign('/simulation/exit');
                                        }
                                    }
                                } else {
                                    if (!window.testMode) {
                                        throw new Error("uniqueId is not found");
                                    }
                                }
                            }
                        },
                        complete: function (xhr, text_status) {
                            if (('timeout' === text_status || xhr.status === 0)  && me.is_connected) {
                                SKApp.isInternetConnectionBreakHappent = true;
                                me.is_connected = false;
                                if( url !== me.api_root + me.connectPath && me.try_connect === false) {
                                    var request = _.first(SKApp.server.requests_queue.where({uniqueId:params.uniqueId}));
                                    request.set('status', 'failed');
                                    var requests = SKApp.server.requests_queue.where({status:'pending'});
                                        requests.forEach(function(request){
                                            if(request.get('ajax') !== null){
                                                request.get('ajax').abort();
                                            }
                                        });

                                        if(me.error_dialog === null) {

                                            // если была фантастическая отправка, то интерфейс заблокирован
                                            SKApp.simulation.isInterfaceWasLoсked = false;
                                            if (0 < $('.display-lock').length) {
                                                SKApp.simulation.trigger('input-lock:stop');
                                                SKApp.simulation.isInterfaceWasLoсked = true;
                                            }
                                            //Чтоб заблокировать ввод с клавиатуры в SocialCalc при розрыве интеренета
                                            //SocialCalc.Keyboard.passThru = true;
                                            me.is_active_game = false;
                                            me.error_dialog = new SKDialogView({
                                                'message': "Пропало Интернет соединение. <br> Симуляция поставлена на паузу.<br>"+
                                                    "Пожалуйста, проверьте Интернет соединение.<br>"+
                                                    "Как только соединение восстановится, <br> мы предложим вам продолжить симуляцию",
                                                'modal': true,
                                                'buttons': []
                                            });

                                            // SKILIKS-5449
                                            // Сообщения про пропажу интернета не видно под сообщением
                                            // про расчёт итогов симуляции
                                            me.error_dialog.$el.css('z-index', 1220);
                                        }
                                        $('.time').addClass('paused');
                                        SKApp.simulation.startPause();
                                    me.tryConnect();
                                }

                            } else if( xhr.status === 200 ) {
                                if( url === me.api_root + me.connectPath ) {
                                    me.is_connected = true;
                                    me.stopTryConnect();
                                    if(null !== me.error_dialog) {
                                        me.error_dialog.remove();
                                        delete me.error_dialog;
                                    }
                                    me.success_dialog = new SKDialogView({
                                        'message': 'Соединение с интернет востановлено!',
                                        'modal': true,
                                        'buttons': [
                                            {
                                                'value': 'Продолжить игру',
                                                'onclick': function () {
                                                    // Если происхои митинг - то не надо снимать игру с паузы
                                                    // а то вермя пойдёт, а игрок не заметит
                                                    // (на екране ведь затемнение "Ушел на встречу, вернусь в ХХ:ХХ.")
                                                    //Чтоб розблокировать ввод с клавиатуры в SocialCalc
                                                    //SocialCalc.Keyboard.passThru = null;
                                                    // если интерфейс был залочен отправкой/приёмом письма фантастическим образом
                                                    // надо вернуть блокировку
                                                    if (SKApp.simulation.isInterfaceWasLoсked) {
                                                        SKApp.simulation.trigger('input-lock:start');
                                                    }

                                                    var restoreAbortedRequests = function() {
                                                        SKApp.server.requests_queue.each(function(request) {
                                                            request.set('is_repeat_request', true);
                                                            request.set('status', 'pending');
                                                            if(request.get('url') === '/index.php/events/getState' || request.get('url') !== '/index.php/simulation/stop'){
                                                                SKApp.server.apiQueue(request.get('url'), request.get('data'), request.get('callback'));
                                                            }else{
                                                                SKApp.server.api(request.get('url'), request.get('data'), request.get('callback'));
                                                            }

                                                        });
                                                        me.success_dialog.remove();
                                                        delete me.success_dialog;
                                                    }

                                                    if (SKApp.simulation.isActiveMeetingPresent()) {
                                                        restoreAbortedRequests();
                                                    } else {
                                                        SKApp.simulation.stopPause(function() {
                                                            $('.time').removeClass('paused');
                                                            restoreAbortedRequests();
                                                        });
                                                    }

                                                    me.is_active_game = true;
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
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            /**
             * Отправляет запрос на сервер
             * @example
             *     SKApp.server.api('todo/get', {}, function (data) {})
             * @param {String} path
             * @param {Object|undefined} params
             * @param {function(data:Object)|undefined} callback
             * @return {$.xhr}
             * @method api
             * @async
             */
            api:function (path, params, callback) {
                try {
                    // this done for SKServer not to make any requests after Simulation stop
                    if(!SKApp.simulation.is_stopped
                        || path == this.connectPath
                        || path == "/index.php/simulation/stop"
                        || path == "simulation/stop") {

                        var ajaxParams = this.getAjaxParams(path, params, callback);
                        var request = _.first(SKApp.server.requests_queue.where({uniqueId:ajaxParams.data.uniqueId}));
                        var ajax = $.ajax(ajaxParams);
                        if(path !== this.connectPath){
                            request.set('ajax', ajax);
                        }
                        return ajax;
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            /**
             * Отправляет запрос на сервер или ставит его в очередь в случае, если такой запрос уже выполняется
             * @method apiQueue
             */
            apiQueue: function (path, params, callback) {
                try {
                    // this done for SKServer not to make any requests after Simulation stop
                    if(!SKApp.simulation.is_stopped || path === "simulation/stop") {
                        var ajaxParams = this.getAjaxParams(path, params, callback);
                        var request = _.first(SKApp.server.requests_queue.where({uniqueId:ajaxParams.data.uniqueId}));
                        var ajax = $.ajaxQueue(ajaxParams);
                        if(path !== this.connectPath){
                            request.set('ajax', ajax);
                        }

                        return ajax;
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            tryConnect: function() {
                try {
                    this.try_connect = true;
                    var me = this;
                        this.request_interval_id = setInterval(function() {
                            me.api(me.connectPath, {invite_id:SKApp.get('invite_id')}, function(){});
                        }, 5000);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            stopTryConnect: function() {
                try {
                    this.try_connect = false;
                    if(this.request_interval_id !== null){
                        clearInterval(this.request_interval_id);
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },
            isRunCallBack:function(uniqueId) {

                if(-1 === this.last_200_request.indexOf(uniqueId)) {
                    if(this.last_200_request.length > 200) {
                        this.last_200_request.shift();
                    }else{
                        this.last_200_request.push(uniqueId);
                    }
                    return true;
                }else{
                    return false;
                }
            }
        });
    return SKServer;
});