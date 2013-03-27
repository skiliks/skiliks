/*global SKImmediateVisitView:true, Backbone, _, SKApp, SKConfig, SKDialogWindow*/

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

            /**
             * @method
             * @param el
             */
            'renderWindow':function (el) {
                var event = this.options.model_instance.get('sim_event');
                var me = this,
                    my_replicas = event.getMyReplicas(),
                    video_src = event.getVideoSrc(),
                    remote_replica = event.getRemoteReplica();
                el.html(_.template(visitTpl, {
                    'remote_replica':remote_replica,
                    'my_replicas':my_replicas,
                    'video_src':video_src,
                    'img_src':event.getImgSrc()
                }));
                el.find('.visit-background-container').css('width', screen.availWidth);
                if (true === SKApp.simulation.config.isMuteVideo) {
                    this.$('video').attr('muted', 'muted');
                }
                this.$('video').on('ended', function () {
                    me.$('video').css('zIndex', 0);
                    if (my_replicas.length === 0) {
                        event.complete();
                        me.remove();
                    }
                });
                var video = el.find('.visit-background');
                video.css('margin-top', '-50px');
                video.css('margin-left', '-20px');
                el.find('.visitor-replica').css('margin-top', '-50px');;

            },

            /**
             * @method
             * @param e
             */
            'doSelectReplica':function (e) {
                var me = this;
                e.preventDefault();
                if("true" !== $(e.currentTarget).attr('data-disabled')) {
                    $(e.currentTarget).attr('data-disabled', 'true');
                    var dialog_id = $(e.currentTarget).attr('data-id');
                    var is_final = $(e.currentTarget).attr('data-is-final');
                    me.options.model_instance.get('sim_event').selectReplica(dialog_id, function () {
                        me.options.model_instance.setLastDialog(dialog_id);
                        if (is_final) {
                            me.options.model_instance.close();
                        }
                    });
                }

            }
        });
    return SKImmediateVisitView;
});