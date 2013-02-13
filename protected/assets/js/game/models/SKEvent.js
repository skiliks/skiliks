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
     * @extends Backbone.Model
     * @class Event model
     */
    window.SKEvent = Backbone.Model.extend(
        /**
         * @lends SKEvent.prototype
         */
        {
            /**
             * Returns slug of event type
             *
             * Phone is phone call
             * Visit is visit
             *
             * @return {'phone'|'immediate-phone'|'immediate-visit'|'visit'}
             */
            getTypeSlug:function () {
                if (this.get('type') === 1) {
                    var first_replica = this.get('data')[0];
                    if (first_replica.dialog_subtype === '1') {
                        return 'phone';
                    } else if (first_replica.dialog_subtype === '2') {
                        return 'immediate-phone';
                    } else if (first_replica.dialog_subtype === '4') {
                        return 'immediate-visit';
                    } else if (first_replica.dialog_subtype === '5') {
                        return 'visit';
                    } else {
                        throw 'Incorrect subtype ' + first_replica.dialog_subtype;
                    }
                } else if (event_types[this.get('type')] === undefined) {
                    throw 'Unknown event type: ' + this.get('type');
                }
                return event_types[this.get('type')];
            },

            /**
             * In dialogs returns replica of foreign character
             * @return {SKDialogReplica}
             */
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
            /**
             * Returns array of player's answers
             * @return {Array}
             */
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
            /**
             * Returns absolute UPL to video background of image
             * @return {String}
             */
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
            /**
             * Returns absolute UPL to image background of image
             * @return {String}
             */
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
                if (audio_src !== null && !audio_src.match(/\.wav/)) {
                    audio_src = null;
                }
                return audio_src ? SKConfig.storageURL + '/sounds/' + audio_src : undefined;
            },
            /**
             * @deprecated use selectReplica
             * @param replica_id
             * @param cb
             */
            select:function (replica_id, cb) {
                SKApp.server.api('dialog/get', {
                    'dialogId':replica_id,
                    'time':    SKApp.user.simulation.getGameTime()
                }, function (data) {
                    if (data.result === 1) {
                        if (cb) {
                            cb(data);
                        }
                        SKApp.user.simulation.parseNewEvents(data.events);
                    }
                });
            },
            /**
             * @return {'completed'|'in progress'|'waiting'}
             */
            getStatus:function () {
                return this.status || 'waiting';
            },
            /**
             *
             * @param {'completed'|'in progress'|'waiting'} status
             */
            setStatus:function (status) {
                console.log('[SKEvent] Event ' + status + ' ' + this.cid);
                /**
                 * @private
                 * @type {"completed"|"in progress"|"waiting"}
                 */
                this.status = status;
                
                if (this.status === 'in progress') {
                    this.trigger('in progress');
                }
                
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
                SKApp.server.api('dialog/get', {
                    'dialogId': dialogId, 
                    'time':     SKApp.user.simulation.getGameTime()
                }, function (data) {
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
            selectReplica:function (replica_id, cb) {
                var me = this;
                SKApp.server.api('dialog/get', {
                    'dialogId': replica_id,
                    'time':     SKApp.user.simulation.getGameTime()
                }, function (data) {
                    if (data.result === 1) {
                        cb();
                        if (me.getStatus() !== 'completed') {
                            me.complete();
                        }
                        SKApp.user.simulation.parseNewEvents(data.events);
                    }
                });
            },
            /**
             * Marks event as completed
             */
            'complete':function () {
                if (this.getStatus() === 'completed') {
                    throw 'This event is already completed';
                }
                this.setStatus('completed');
            }
        });
})();