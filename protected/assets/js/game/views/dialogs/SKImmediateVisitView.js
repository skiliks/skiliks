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
        {     'el':'body .visitor-container',

            'events':_.defaults({
                'click .replica-select':'doSelectReplica'
            }, SKWindowView.prototype.events),

            /**
             * Constructor
             * @method initialize
             */
            'initialize':function () {
                var me = this;
                this.listenTo(this.options.model_instance, 'refresh', function () {
                    me.render();
                });
                SKWindowView.prototype.initialize.call(this);
            },

            center: function() {
                //$(".sim-window").css('top', '0px');
                //$(".sim-window").css('left', '0px');

            },

            /**
             * @method
             * @param el
             */
            'renderWindow':function (el) {
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
                if (!is_first_replica) {
                    if (video_src) {
                        el.find('video.visit-background').on('loadeddata', renderFn);
                    } else if (image_src) {
                        el.find('img.visit-background').on('load', renderFn);
                    } else {
                        renderFn();
                    }
                } else {
                    renderFn();
                }

                function renderFn() {
                    var oldContent = el.children('.visit-background-container'),
                        newContent = el.find('.placeholder .visit-background-container');

                    if (oldContent.length) {
                        oldContent.replaceWith(newContent);
                        el.find('.placeholder').remove();
                    } else {
                        el.find('.placeholder').replaceWith(newContent);
                    }

                    el.find('.visit-background-container').css('width', screen.availWidth);
                    if (true === SKApp.simulation.config.isMuteVideo) {
                        me.$('video').attr('muted', 'muted');
                    }

                    me.$('video').on('ended', function () {
                        me.$('video').css('zIndex', 0);
                        if (my_replicas.length === 0) {
                            event.complete();
                            me.options.model_instance.close();
                            me.remove();
                        } else if (!SKApp.simulation.isDebug()) {
                            el.find('.char-reply').removeClass('hidden');
                            el.find('.visitor-reply').removeClass('hidden');
                        }
                    });
                    // this stupid code is a workaround of Google Chrome bug where video does not start
                    me.$('video').on('canplay', function() {
                        this.play();
                    } );

                    if (0 === me.$('video').length) {
                        el.find('.char-reply').removeClass('hidden');
                    }

                    var video = el.find('.visit-background');
                    video.css('margin-top', '-50px');
                    video.css('margin-left', '-20px');
                    el.find('.visitor-replica').css('margin-top', '-50px');
                }
            },

            /**
             * @method
             * @param e
             */
            'doSelectReplica':function (e) {
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

            }
        });
    return SKImmediateVisitView;
});