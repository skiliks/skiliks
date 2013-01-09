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
            SKApp.user.simulation.events.on('dialog:end', function () {
                me.close();
            });

        },
        'close': function () {
            this.visitor_entrance_window.close();
            this.off('dialog:end');
            this.$el.html('');
        },
        'render':function () {
            var replicas = this.options.event.get('data'),
                me = this,
                my_replicas = [],
                remote_replica,
                video_src = '';
            replicas.forEach(function (replica) {
                if (replica.ch_to === '1') {
                    remote_replica = replica;
                    video_src = replica.sound;
                } else {
                    my_replicas.push(replica);
                    video_src = video_src || replica.sound;

                }
            });
            this.visitor_entrance_window = new SKDialogWindow('visitor', 'visitorEntrance', replicas[0].id);
            this.visitor_entrance_window.open();
            this.$el.html(_.template($('#visit_template').html(), {
                'remote_replica':remote_replica,
                'my_replicas':my_replicas,
                'video_src': video_src ? SKConfig.assetsUrl + '/videos/' + video_src : undefined
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
                    SKApp.user.simulation.parseNewEvents(data.events);
                    me.undelegateEvents();
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