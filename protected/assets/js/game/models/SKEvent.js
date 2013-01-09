/*global Backbone, SKConfig, SKApp*/
(function () {
    "use strict";
    var event_types = {
        'M':'mail',
        'MS':'mail',
        'D':'document',
        'P':'plan',
        2:'event'
    };

    window.SKEvent = Backbone.Model.extend({
        'initialize':function () {
            this.completed = false;
        },
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
            return video_src ? SKConfig.assetsUrl + '/videos/' + video_src : undefined;
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
            return img_src ? SKConfig.assetsUrl + '/dialog_images/' + img_src : undefined;
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
            return audio_src ? SKConfig.assetsUrl + '/sounds/' + audio_src : undefined;
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
        'complete':function () {
            if (this.completed === true) {
                throw 'This event is already completed';
            }
            this.completed = true;
        }
    });
})();