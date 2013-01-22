/*global _, SKWindowView, SKDialogWindow, SKApp */

$(function () {
    "use strict";

    window.SKPhoneDialogView = SKWindowView.extend({
        title: "Телефон",
        'events':_.defaults({
            'click .replica-select':   'doSelectReplica',
        }, SKWindowView.prototype.events),
        
        remove: function () {
            this.off('dialog:end');
            SKWindowView.prototype.remove.call(this);
        },
        initialize:function() {
            var me = this;
            this.listenTo(this.options.model_instance, 'refresh', function () {
                me.render();
            });
            SKWindowView.prototype.initialize.call(this);
        },
        renderContent:function (window_el) {
            var event = this.options.model_instance.get('sim_event'),
                me = this,
                my_replicas = event.getMyReplicas(),
                remote_replica = event.getRemoteReplica();
                console.log(remote_replica);
            var callInHtml = _.template($('#Phone_Dialog').html(), {
                'remote_replica':remote_replica,
                'my_replicas':my_replicas,
                'audio_src': event.getAudioSrc()
            });
            window_el.html(callInHtml);
            this.$('audio').on('ended', function(){
                if (my_replicas.length === 0) {
                    event.select(remote_replica.id, function () {
                        me.options.model_instance.close();
                    });
                }
            });
        },
        
        doSelectReplica:function (e) {
            var me = this;
            e.preventDefault();
            var event = this.options.event;
            var dialog_id = $(e.currentTarget).attr('data-id');
            var is_final = $(e.currentTarget).attr('data-is-final');

            SKApp.server.api('dialog/get', {'dialogId':dialog_id}, function (data) {
                if (data.result === 1) {
                    me.options.model_instance.setLastDialog(dialog_id);
                    /* TODO refactor */
                    if (is_final) {
                        me.options.model_instance.close();
                    }
                    SKApp.user.simulation.parseNewEvents(data.events);
                    /*if (flag === 1) {
                     me.closedialogController();
                     } */
                }
            });
        }
    });
});
