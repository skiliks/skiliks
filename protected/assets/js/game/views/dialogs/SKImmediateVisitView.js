/*global SKImmediateVisitView:true, Backbone, _, SKApp, SKConfig, SKDialogWindow, $ */

var SKImmediateVisitView;
var min_w = 300; // minimum video width allowed
var vid_w_orig;  // original video dimensions
var vid_h_orig;

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

            /**
             * Constructor
             * @method initialize
             */
            'initialize':function () {
                try {
                    var me = this;
                    this.listenTo(this.options.model_instance, 'refresh', function () {
                        console.log("Window refresh");
                        me.render();
                    });
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
                    console.log("SKImmediateVisitView.renderWindow");
                    var me = this,
                        event = this.options.model_instance.get('sim_event'),
                        my_replicas = event.getMyReplicas(),
                        video_src = event.getVideoSrc(),
                        image_src = event.getImgSrc(),
                        remote_replica = event.getRemoteReplica(),
                        media, text;

                    text =  _.template(visitTpl, {
                        'remote_replica': remote_replica,
                        'my_replicas': my_replicas,
                        'video_src': video_src,
                        'img_src': image_src
                    });
                    var is_first_replica = !el.html();
                    $('<div class="hidden placeholder" />').html(text).appendTo(el);
                    console.log($('div.hidden.placeholder'));
                    if (!is_first_replica) {
                        if (video_src) {
                            el.find('video.visit-background').on('loadeddata', function(){
                                console.log('video.visit-background');
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
                        console.log('VisitView el',el.get(0));
                        if (oldContent.length) {
                            //Вот здесь проблемма с отображением для E3.4
                            console.log('VisitView oldContent');
                            oldContent.replaceWith(newContent);
                            el.find('.placeholder').remove();

                        } else {
                            console.log('VisitView newContent');
                            el.find('.placeholder').replaceWith(newContent);
                        }

                        el.find('.visit-background-container').css('width', screen.availWidth);

                        var duration;
                        if(SKApp.simulation.isDebug() || null === remote_replica){
                            console.log('set duration if', 0);
                            duration = 0;
                        }else{
                            console.log('set duration else', remote_replica.duration);
                            duration = parseInt(remote_replica.duration, 0)*1000;
                        }
                        // Для дев режима, последняя реплика в диалоге, если нет вариантов ответа - сразу исчезает.
                        // Из-за этого тесты которые проверяют отображение реплик валятся
                        // 5 сек задержки должно хватать, но если не хватит можно увеличить
                        if (0 == my_replicas.length) {
                            console.log('set 10000');
                            duration = 10000;
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

                        jQuery(function() { // runs after DOM has loaded

                            vid_w_orig = 1280;
                            vid_h_orig = 800;

                            jQuery(window).resize(function () { resizeToCover(); });
                            jQuery(window).trigger('resize');
                        });
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
                                console.log("Is final replica window must be close");
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
            }
        });
    return SKImmediateVisitView;
});

function resizeToCover() {

    // set the video viewport to the window size
    jQuery('.visit-background-container').width(jQuery(window).width());
    jQuery('.visit-background-container').height(jQuery(window).height());

    // use largest scale factor of horizontal/vertical
    var scale_h = jQuery(window).width() / vid_w_orig;
    var scale_v = jQuery(window).height() / vid_h_orig;
    var scale = scale_h > scale_v ? scale_h : scale_v;

    // now scale the video
    jQuery('video').width(scale * vid_w_orig) + 10;
    jQuery('video').height(scale * vid_h_orig) + 10;
    // and center it by scrolling the video viewport
    jQuery('.visit-background-container').scrollLeft((jQuery('video').width() - jQuery(window).width()) / 2);
    jQuery('.visit-background-container').scrollTop((jQuery('video').height() - jQuery(window).height()) / 2);

    if(jQuery(window).width() / jQuery(window).height() < 1.6) {
        jQuery('video').css("margin-left", (jQuery('video').width() - jQuery(window).width()) / 2);
    } else {
        console.log("It's not doing");
        jQuery('video').css('margin-left', '-20px');
    }
};