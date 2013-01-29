/*global _, SKWindowView, SKDialogWindow, SKApp */

$(function () {
    "use strict";

    window.SKPhoneDialogView = SKWindowView.extend({
        title: "Телефон",
        'events':_.defaults({
            'click .phone-draw-menu':'getMenu',
            'click .replica-select':   'doSelectReplica'
        }, SKWindowView.prototype.events),
        
        remove: function () {
            var event = this.options.model_instance.get('sim_event');
            if (event.getStatus() !== 'completed') {
                event.complete();
            }
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
                    event.selectReplica(remote_replica.id, function () {
                        me.options.model_instance.close();
                    });
                }
            });
        },
        getMenu: function(event){
            this.options.model_instance.close();
            SKApp.user.simulation.window_set.toggle('phone','phoneMain');
        },

        doSelectReplica:function (e) {
            var me = this;
            e.preventDefault();
            var event = this.options.model_instance.get('sim_event');
            var dialog_id = $(e.currentTarget).attr('data-id');
            var is_final = $(e.currentTarget).attr('data-is-final');
            event.selectReplica(dialog_id, function () {
                me.options.model_instance.setLastDialog(dialog_id);
                /* TODO refactor */
                if (is_final) {
                    me.options.model_instance.close();
                }
            });
        }
    });
});
