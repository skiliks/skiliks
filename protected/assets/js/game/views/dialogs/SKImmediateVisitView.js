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
            addClass: 'visitor-window',
            'el':'body .visitor-container',

            'events':_.defaults({
                'click .replica-select':'doSelectReplica'
            }, SKWindowView.prototype.events),

            video_width_original : 1280,
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
             * @method
             * @param el
             */
            'renderWindow':function (el) {
                try {
                    var me = this,
                        event = this.options.model_instance.get('sim_event'),
                        my_replicas = event.getMyReplicas(),
                        video_src = event.getVideoSrc(),
                        image_src = event.getImgSrc(),
                        remote_replica = event.getRemoteReplica(),
                        media, text;

                    if ($.browser['msie'] == true) {
                        video_src = video_src.replace('.webm', 'mp4')
                    }

                    text =  _.template(visitTpl, {
                        'remote_replica': remote_replica,
                        'my_replicas': my_replicas,
                        'video_src': video_src,
                        'img_src': image_src
                    });
                    var is_first_replica = !el.html();
                    $('<div class="hidden placeholder" />').html(text).appendTo(el);
                    if (!is_first_replica) {
                        if (video_src) {
                            el.find('video.visit-background').on('loadeddata', function(){
                                renderFn(remote_replica);
                            });
                        } else if (image_src) {
                            el.find('img.visit-background').on('load', function(){
                                renderFn(remote_replica);
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

                function renderFn(remote_replica) {
                    try {

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
                            throw new Error('remote_replica must be not null!');
                        }else{
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
                            } else if (!SKApp.simulation.isDebug()) {
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
             * @method
             * @param e
             */
            'doSelectReplica':function (e) {
                try {
                    var me = this;
                    e.preventDefault();
                    if("true" !== $(e.currentTarget).attr('data-disabled')) {
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
                    }
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

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

                if($(window).width() / $(window).height() < 1.6) {
                    me.$('video').css("margin-left", -(me.$('video').width() - $(window).width()) / 2);
                } else {
                    me.$('video').css('margin-left', '-20px');
                }
            },

            onResize : function() {
                var me = this;
                window.SKWindowView.prototype.onResize.apply(this);
                me.doResizeVideo();
            }
        });
    return SKImmediateVisitView;
});