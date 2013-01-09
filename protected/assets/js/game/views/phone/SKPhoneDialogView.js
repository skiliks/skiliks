/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$(function () {
    "use strict";

    window.SKPhoneDialogView = window.SKWindowView.extend({
        el: 'body .phone-dialog-div',
        initialize:function (){
            var me = this;
            this.render();

        },
        'events': {
            'click .replica-select':'doSelectReplica'
        },
        close: function () {
            this.off('dialog:end');
            this.talk_window.close();
            this.$el.html('');
            this.undelegateEvents();

        },
        render:function () {
            var event = this.options.event;
            var me = this,
                my_replicas = event.getMyReplicas(),
                remote_replica = event.getRemoteReplica();

            this.activeSubScreen = 'phoneTalk';
            this.talk_window = new SKDialogWindow('phone', 'phoneTalk', event ? event.get('data')[0].id : undefined);
            this.talk_window.open();
            console.log(event.get('data'));
            var callInHtml = _.template($('#Phone_Dialog').html(), {
                'remote_replica':remote_replica,
                'my_replicas':my_replicas,
            });
            this.$el.html(callInHtml);
        },
        doSelectReplica:function (e) {
            var me = this;
            e.preventDefault();
            var dialog_id = $(e.currentTarget).attr('data-id');
            SKApp.server.api('dialog/get', {'dialogId':dialog_id}, function (data) {
                if (data.result === 1) {
                    me.talk_window.setLastDialog(dialog_id);
                    /* TODO refactor */
                    if (data.events && data.events[0] && data.events[0].data && data.events[0].data[0] && data.events[0].data[0].step_number === '1' &&
                        data.events[0].data[0].dialog_subtype === '4') {
                        me.talk_window.switchDialog(data.events[0].data[0].id);
                    }
                    me.close();
                    SKApp.user.simulation.parseNewEvents(data.events);
                    /*if (flag === 1) {
                     me.closedialogController();
                     } */
                }
            });
        }
    });
});
