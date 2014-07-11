/*global SKImmediateVisitView:true, Backbone, _, SKApp, SKConfig, SKDialogWindow, $ */

var SKImmediateVisitView;

define([
        "game/views/SKWindowView",

        "text!game/jst/visit/visit_template.jst"
    ], function (
        SKWindowView,

        visitTpl
    ) {
    "use strict";
    /**
     * @class SKImmediateVisitView
     * @augments Backbone.View
     */
    SKImmediateVisitView = SKWindowView.extend(
        /** @lends SKImmediateVisitView.prototype */
        {
            /**
             * Делает окно с визитом уникальным для поиска по CSS
             *
             * @var String addClass
             */
            addClass: 'visitor-window',

            /**
             * Базовый HTML DOM контейнер, должен быть уникальным
             * @var jQuery el
             */
            'el': 'body .visitor-container',

            /**
             * События DOM на которые должна реагировать данная view
             * @var Array events
             */
            'events':_.defaults({
                'click .replica-select':'doSelectReplica'
            }, SKWindowView.prototype.events),

            /**
             * Используется для мастабирования видео.
             * В данный момент все видеовставки для диалогов имеют только 1 размер 1280х800.
             */

            /**
             * @var Number video_width_original
             */
            video_width_original : 1280,

            /**
             * @var Number video_height_original
             */
            video_height_original : 800,

            /**
             * Constructor
             * @method initialize
             */
            'initialize':function () {
                try {
                    var me = this;
                    this.listenTo(this.options.model_instance, 'refresh', function () {
                        me.render();
                    });
                    // original video height and width
                    SKWindowView.prototype.initialize.call(this);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Центрирование видео
             */
            center: function() {
                try {
                    $(".sim-window.visitor-window").css('height', '100%');
                    $(".sim-window.visitor-window").css('width', '100%');
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @param jQuery el, 'body .visitor-container'
             */
            'renderWindow':function (el) {
                try {
                    var me = this,
                        event = this.options.model_instance.get('sim_event'),
                        my_replicas = event.getMyReplicas(),
                        video_src = event.getVideoSrc(),
                        image_src = event.getImgSrc(),
                        remote_replica = event.getRemoteReplica(), // generated in php: DialogService.dialogToArray()
                        poster_src = event.getPosterSrc(video_src),
                        text;

                    text =  _.template(visitTpl, {
                        'remote_replica': remote_replica,
                        'my_replicas':    my_replicas,
                        'video_src':      video_src,
                        'img_src':        image_src,
                        'poster_src':     poster_src
                    });

                    var is_first_replica = !el.html();
                    $('<div class="hidden placeholder" />').html(text).appendTo(el);
                    var remote_replica_id = remote_replica?remote_replica.id : 0;
                    if (!is_first_replica) {
                        if (video_src) {
                            el.find('video.visit-background').on('loadeddata', function(){
                                if(remote_replica_id !== el.find('video.visit-background').data('remote_replica_id')){
                                    renderFn(remote_replica);
                                }
                            });
                            el.find('video.visit-background').on('error', function(event){
                                if(remote_replica_id !== el.find('video.visit-background').data('remote_replica_id')){
                                    renderFn(remote_replica);
                                }
                            });
                        } else if (image_src) {
                            el.find('img.visit-background').on('load', function(){
                                if(remote_replica_id !== el.find('video.visit-background').data('remote_replica_id')){
                                    renderFn(remote_replica);
                                }
                            });
                            el.find('img.visit-background').on('error', function(){
                                if(remote_replica_id !== el.find('video.visit-background').data('remote_replica_id')){
                                    renderFn(remote_replica);
                                }
                            });
                        } else {
                            renderFn(remote_replica);
                        }
                    } else {
                        renderFn(remote_replica);
                    }
                    me.delegateEvents();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }

                /**
                 * Отрисовывает картинку или видео, входящую реплику к персонажу и реплики-ответы. ()
                 *
                 * @param Object remote_replica, generated in php: DialogService.dialogToArray()
                 */
                function renderFn(remote_replica) {
                    try {
                        var remote_replica_id = remote_replica?remote_replica.id : 0;
                        el.find('video.visit-background').data('remote_replica_id', remote_replica_id);

                        var oldContent = el.children('.visit-background-container'),
                            newContent = el.find('.placeholder .visit-background-container');
                        if (oldContent.length) {
                            oldContent.replaceWith(newContent);
                            el.find('.placeholder').remove();

                        } else {
                            el.find('.placeholder').replaceWith(newContent);
                        }

                        el.find('.visit-background-container').css('width', screen.availWidth);


                        var duration;
                        if(null === remote_replica){
                            // такое бывает когда Главный герой сам говорит первым
                            // то есть duration реплики к Главному герою навна нулю
                            duration = 0;
                        } else {
                            duration = parseInt(remote_replica.duration, 0)*1000;
                        }
                        // Для дев режима, последняя реплика в диалоге, если нет вариантов ответа - сразу исчезает.
                        // Из-за этого тесты которые проверяют отображение реплик валятся
                        // 5 сек задержки должно хватать, но если не хватит можно увеличить
                        if (SKApp.simulation.isDebug() && 0 == my_replicas.length) {
                            duration = 5000;
                        }

                        setTimeout(function(){
                            me.$('video').css('zIndex', 0);
                            if (my_replicas.length === 0) {
                                event.selectReplica(remote_replica.id, function () {
                                    me.options.model_instance.setLastDialog(remote_replica.id);
                                    if (remote_replica.is_final_replica === "1") {
                                        me.options.model_instance.setOnTop();
                                        me.options.model_instance.close();
                                    }
                                });
                            } else if (true != SKApp.simulation.isDebug()) {
                                // this is PROMO mode
                                el.find('.char-reply').removeClass('hidden');
                                el.find('.visitor-reply').removeClass('hidden');
                            }
                            me.delegateEvents();
                        }, duration);

                        // this stupid code is a workaround of Google Chrome bug where video does not start
                        me.$('video').on('canplay', function() {
                            this.play();
                        } );

                        if (0 === me.$('video').length) {
                            el.find('.char-reply').removeClass('hidden');
                        }

                        var video = el.find('.visit-background');
                        video.css('margin-top', '-45px');
                        video.css('margin-left', '-20px');
                        el.find('.visitor-replica').css('margin-top', '-50px');
                        me.doResizeVideo();
                    } catch(exception) {
                        if (window.Raven) {
                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                        }
                    }
                }
            },

            /**
             * @param OnClockEvent e
             */
            'doSelectReplica':function (e) {
                try {
                    var me = this;
                    e.preventDefault();
                    if("true" !== $(e.currentTarget).attr('data-disabled')) {

                        // Когда пользователь кликнул по реплике - данные о выбранной реплике передаются на сервер.
                        // Но пока придёт ответ пользователь успеет кликнуть несколько раз,
                        // поэтому мы блокируем обработку всех кликов по репликам-ответам, после первого клика
                        $('#dialogControllerAnswers li').each(function(index, element) {
                            $(element).find('a').attr('data-disabled', 'true');
                        });

                        var dialog_id = $(e.currentTarget).attr('data-id');
                        var is_final = $(e.currentTarget).attr('data-is-final');
                        me.options.model_instance.get('sim_event').selectReplica(dialog_id, function () {

                            me.options.model_instance.setLastDialog(dialog_id);
                            if (is_final) {
                                me.options.model_instance.setOnTop();
                                me.options.model_instance.close();
                            }
                        });

                        var ids = new Array();
                        $('.replica-select').each(function(i){
                            ids[i] = $(this).attr('data-id');
                        });

                        setTimeout(_.bind(me.restoreReplicasAccessibility, me), 60*1000, ids); // 1 min
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            restoreReplicasAccessibility: function(ids) {
                try {
                    if (window.Raven) {
                        window.Raven.captureMessage('restoreReplicasAccessibility,' + SKApp.simulation.getGameTime(true));
                    }

                    var me = this;

                    var isNoOneReplicaSent = true;
                    for (var i in ids) {
                        var id = ids[i];
                        var dialogHistory = SKApp.simulation.dialogsHistory.where({'replica_id': id, 'is_sent': true});
                        if (0 < dialogHistory.length) {
                            isNoOneReplicaSent = false;
                        }
                    }

                    if (isNoOneReplicaSent) {
                        $('.replica-select').removeAttr('data-disabled');
                        me.options.model_instance.setOnTop();
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * Используется как реаукия на изменение размеров окна браузера
             */
            'doResizeVideo' : function () {
                var me = this;
                me.$('.visit-background-container').width($(window).width());
                me.$('.visit-background-container').height($(window).height());

                // use largest scale factor of horizontal/vertical
                var scale_height = $(window).width() / me.video_width_original;
                var scale_width = $(window).height() / me.video_height_original;
                var scale = scale_height > scale_width ? scale_height : scale_width;

                me.$('video').width(scale * me.video_width_original + 10);
                me.$('video').height(scale * me.video_height_original + 10);
                // and center it by scrolling the video viewport
                me.$('.visit-background-container').scrollLeft((me.$('video').width() - $(window).width()) / 2);
                me.$('.visit-background-container').scrollTop((me.$('video').height() - $(window).height()) / 2);

                // 1280/800 = 1.6
                if($(window).width() / $(window).height() < 1.6) {
                    me.$('video').css("margin-left", ($(window).width() - me.$('video').width()) / 2);
                } else {
                    me.$('video').css('margin-left', '-20px');
                }
            },

            /**
             * При изменении размера окна, надо смасштабировать видео под новый размер окна.
             */
            onResize : function() {
                var me = this;
                window.SKWindowView.prototype.onResize.apply(this);
                me.doResizeVideo();
            }
        });
    return SKImmediateVisitView;
});