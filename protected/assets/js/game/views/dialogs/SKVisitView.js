/*global Backbone, _, SKApp, SKConfig, SKDialogWindow*/
(function () {
    "use strict";
    window.SKVisitView = Backbone.View.extend({
        'el':'body .visitor-container',
        'events':{
            'click .replica-select':'doSelectReplica'
        },
        'initialize':function () {
            var me = this;
            this.render();


        },
        'close':function () {
            this.visitor_entrance_window.close();
            this.undelegateEvents();
            this.$el.html('');
        },
        'render':function () {
            var event = this.options.event;
            var me = this,
                my_replicas = event.getMyReplicas(),
                video_src = event.getVideoSrc(),
                remote_replica = event.getRemoteReplica();
            if (this.visitor_entrance_window === undefined || !this.visitor_entrance_window.is_opened) {
                this.visitor_entrance_window = new SKDialogWindow({name:'visitor', subname:'visitorEntrance', sim_event:event});
                this.visitor_entrance_window.open();
            } else {
                this.visitor_entrance_window.set('sim_event', event);
            }
            console.log('SKApp.user.simulation.config.isMuteVideo: ', SKApp.user.simulation.config.isMuteVideo);
            var muteTag = '';
            if (true === SKApp.user.simulation.config.isMuteVideo) {
                muteTag = 'muted';
            }
            this.$el.html(_.template($('#visit_template').html(), {
                'remote_replica':remote_replica,
                'my_replicas':my_replicas,
                'video_src': video_src,
                'img_src': event.getImgSrc(),
                'mute_attribute': muteTag
            }));
            this.$('video').on('ended', function () {
                me.$('video').css('zIndex', 0);
                if (my_replicas.length === 0) {
                    me.options.event.complete();
                    me.close();
                }
            });

        },
        'doSelectReplica':function (e) {
            var me = this;
            e.preventDefault();
            var dialog_id = $(e.currentTarget).attr('data-id');
            var is_final = $(e.currentTarget).attr('data-is-final');
            this.options.event.selectReplica(dialog_id, function () {
                me.visitor_entrance_window.setLastDialog(dialog_id);
                /* TODO refactor */
                if (is_final) {
                    me.close();
                }
            });
        },
        'nextDialog':function () {

        }
    });
})();