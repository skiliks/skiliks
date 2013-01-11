/*global _, SKWindowView, SKDialogWindow, SKApp */

$(function () {
    "use strict";

    window.SKPhoneDialogView = SKWindowView.extend({
        el: 'body .phone-dialog-div',
        initialize:function (){
            this.render();

        },
        'events':_.defaults({
            'click .replica-select':'doSelectReplica'
        }, SKWindowView.prototype.events),
        remove: function () {
            this.off('dialog:end');
            this.talk_window.close();
            SKWindowView.prototype.remove.call(this);
        },
        renderWindow:function (window_el) {
            var event = this.options.event,
                me = this,
                my_replicas = event.getMyReplicas(),
                remote_replica = event.getRemoteReplica();

            this.activeSubScreen = 'phoneTalk';
            this.talk_window = new SKDialogWindow('phone', 'phoneTalk', event ? event.get('data')[0].id : undefined);
            this.talk_window.open();
            var callInHtml = _.template($('#Phone_Dialog').html(), {
                'remote_replica':remote_replica,
                'my_replicas':my_replicas,
                'audio_src': event.getAudioSrc()
            });
            window_el.html(callInHtml);
            this.$('audio').on('ended', function(){
                if (my_replicas.length === 0) {
                    event.select(remote_replica.id, function () {
                        me.remove();
                    });
                }
            });
        },
        doSelectReplica:function (e) {
            var me = this;
            e.preventDefault();
            var event = this.options.event;
            var dialog_id = $(e.currentTarget).attr('data-id');

            SKApp.server.api('dialog/get', {'dialogId':dialog_id}, function (data) {
                if (data.result === 1) {
                    me.talk_window.setLastDialog(dialog_id);
                    /* TODO refactor */
                    if (data.events && data.events[0] && data.events[0].data && data.events[0].data[0] && data.events[0].data[0].step_number === '1' &&
                        data.events[0].data[0].dialog_subtype === '4') {
                        me.talk_window.switchDialog(data.events[0].data[0].id);
                    }
                    me.remove();
                    SKApp.user.simulation.parseNewEvents(data.events);
                    /*if (flag === 1) {
                     me.closedialogController();
                     } */
                }
            });
        }
    });
});
