/*global SKEvent:true, Backbone, SKConfig, SKApp*/
(function () {
    "use strict";
    var event_types = {
        'M':'mail',
        'MS':'mail',
        'D':'document',
        'P':'plan',
        2:'event'
    };

    /**
     * @class Event model
     */
    window.SKEvent = Backbone.Model.extend(
        /**
         * @lends SKEvent.prototype
         */
        {
            'initialize':function () {
                this.completed = false;
            },
            /**
             * Returns slug of event type
             *
             * @memberOf SKEvent
             * @return {string}
             */
            getTypeSlug:function () {
                if (this.get('type') === 1) {
                    if (this.get('data')[0].dialog_subtype === '1') {
                        return 'phone';
                    } else if (this.get('data')[0].dialog_subtype === '2') {
                        return 'immediate-phone';
                    } else if (this.get('data')[0].dialog_subtype === '4') {
                        return 'immediate-visit';
                    } else if (this.get('data')[0].dialog_subtype === '5') {
                        return 'visit';
                    } else {
                        throw 'Incorrect subtype ' + this.get('data')[0].dialog_subtype;
                    }
                } else if (event_types[this.get('type')] === undefined) {
                    throw 'Unknown event type: ' + this.get('type');
                }
                return event_types[this.get('type')];
            },
            getRemoteReplica:function () {
                var replicas = this.get('data');
                var remote_replica = null;
                replicas.forEach(function (replica) {
                    if (replica.ch_to === '1') {
                        remote_replica = replica;
                    }
                });
                return remote_replica;
            },
            getMyReplicas:function () {
                var replicas = this.get('data');
                var my_replicas = [];
                replicas.forEach(function (replica) {
                    if (replica.ch_to !== '1') {
                        my_replicas.push(replica);
                    }
                });
                return my_replicas;
            },
            getVideoSrc:function () {
                var replicas = this.get('data');
                var video_src;
                replicas.forEach(function (replica) {
                    video_src = video_src || replica.sound;
                });
                if (!video_src.match(/\.webm$/)) {
                    video_src = undefined;
                }
                return video_src ? SKConfig.storageURL + '/videos/' + video_src : undefined;
            },
            getImgSrc:function () {
                var replicas = this.get('data');
                var img_src = null;
                replicas.forEach(function (replica) {
                    img_src = img_src || replica.sound;
                });
                if (!img_src.match(/\.png$/)) {
                    img_src = undefined;
                }
                return img_src ? SKConfig.storageURL + '/dialog_images/' + img_src : undefined;
            },
            getAudioSrc:function () {
                var replicas = this.get('data');
                var audio_src = null;
                replicas.forEach(function (replica) {
                    audio_src = audio_src || replica.sound;
                });
                if (!audio_src.match(/\.wav/)) {
                    audio_src = null;
                }
                return audio_src ? SKConfig.storageURL + '/sounds/' + audio_src : undefined;
            },
            select:function (replica_id, cb) {
                SKApp.server.api('dialog/get', {'dialogId':replica_id}, function (data) {
                    if (data.result === 1) {
                        if (cb) {
                            cb(data);
                        }
                        SKApp.user.simulation.parseNewEvents(data.events);
                    }
                });
            },
            getStatus:function () {
                return this.status || 'waiting';
            },
            /**
             *
             * @param {'completed'|'in progress'|'waiting'} status
             */
            setStatus:function (status) {
                this.status = status;
                if (this.status === 'completed') {
                    this.trigger('complete');
                }
            },
            ignore:function (cb) {
                if (this.getTypeSlug() !== 'phone') {
                    throw 'You can ignore only phone calls';
                }

                var dialogId = this.get('data')[2].id;
                // @todo: здесь сложно накручено но надо развязать
                // и перенести игнорирование во вьюху SKPhoneCallView
                SKApp.server.api('dialog/get', {'dialogId':dialogId, 'time':SKApp.user.simulation.getGameTime()}, function (data) {
                    SKApp.user.simulation.parseNewEvents(data.events);
                    if (cb !== undefined) {
                        cb();
                    }
                });
            },
            /**
             * Selects replica in dialog
             *
             * @param replica_id
             * @param cb
             */
            selectReplica:function(replica_id, cb) {
                this.complete();
                SKApp.server.api('dialog/get', {'dialogId':replica_id}, function (data) {
                    if (data.result === 1) {
                        cb();
                        SKApp.user.simulation.parseNewEvents(data.events);
                    }
                });
            },
            'complete':function () {
                if (this.getStatus() === 'completed') {
                    throw 'This event is already completed';
                }
                this.setStatus('completed');
            }
        });
})();