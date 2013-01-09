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
        'close': function () {
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
            this.visitor_entrance_window = new SKDialogWindow('visitor', 'visitorEntrance', event.get('data')[0].id);
            this.visitor_entrance_window.open();
            this.$el.html(_.template($('#visit_template').html(), {
                'remote_replica':remote_replica,
                'my_replicas':my_replicas,
                'video_src': video_src,
                'img_src': event.getImgSrc()
            }));
            this.$('video').on('ended', function(){
                me.$('video').css('zIndex', 0);
                if (my_replicas.length === 0) {
                    me.close();
                }
            });

        },
        'doSelectReplica':function (e) {
            var me = this;
            e.preventDefault();
            var dialog_id = $(e.currentTarget).attr('data-id');
            SKApp.server.api('dialog/get', {'dialogId':dialog_id}, function (data) {
                if (data.result === 1) {
                    me.visitor_entrance_window.setLastDialog(dialog_id);
                    /* TODO refactor */
                    if (data.events && data.events[0] && data.events[0].data && data.events[0].data[0] && data.events[0].data[0].step_number === '1' &&
                        data.events[0].data[0].dialog_subtype === '4') {
                        me.visitor_entrance_window.switchDialog(data.events[0].data[0].id);
                    }
                    me.close();
                    SKApp.user.simulation.parseNewEvents(data.events);
                    /*if (flag === 1) {
                     me.closedialogController();
                     } */
                }
            });
        },
        'nextDialog':function () {

        }
    });
})();