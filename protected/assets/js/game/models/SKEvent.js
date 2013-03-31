/*global SKEvent:true, Backbone, SKConfig, SKApp*/
define([], function () {
    "use strict";
    /**
     * @class
     */
    var event_types = {
        'M':  'mail',
        'MS': 'mail-send',
        'D':  'document',
        'P':  'plan',
        2:    'event'
    };

    /**
     * Объект события
     *
     * @class SKEvent
     * @augments Backbone.Model
     */
    window.SKEvent = Backbone.Model.extend({
        /**
         * @lends SKEvent.prototype
         */

        /**
         * Returns slug of event type
         *
         * Phone is phone call
         * Visit is visit
         *
         * @return {'phone'|'immediate-phone'|'immediate-visit'|'visit'}
         * @method getTypeSlug
         */
        getTypeSlug: function () {
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
         * @method getRemoteReplica
         * @return {SKDialogReplica}
         */
        getRemoteReplica: function () {
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
         *
         * @method
         * @return {Array}
         */
        getMyReplicas: function () {
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
         *
         * @method
         * @return {String}
         */
        getVideoSrc: function () {
            var replicas = this.get('data');
            var video_src;
            replicas.forEach(function (replica) {
                video_src = video_src || replica.sound;
            });
            if (!video_src.match(/\.webm$/)) {
                video_src = undefined;
            }
            return video_src ? SKApp.get('storageURL') + '/videos/' + video_src : undefined;
        },

        /**
         * Returns absolute UPL to image background of image
         *
         * @method
         * @return {String}
         */
        getImgSrc: function () {
            var replicas = this.get('data');
            var img_src = null;
            replicas.forEach(function (replica) {
                img_src = img_src || replica.sound;
            });
            if (!img_src.match(/\.jpeg$/)) {
                img_src = undefined;
            }
            return img_src ? SKApp.get('storageURL') + '/dialog_images/' + img_src : undefined;
        },

        /**
         * @method
         * @returns {string}
         */
        getAudioSrc: function () {
            var replicas = this.get('data');
            var audio_src = null;
            replicas.forEach(function (replica) {
                audio_src = audio_src || replica.sound;
            });
            if (audio_src !== null && !audio_src.match(/\.wav/)) {
                audio_src = null;
            }
            return audio_src ? SKApp.get('storageURL') + '/sounds/' + audio_src : undefined;
        },

        /**
         * @method
         * @deprecated use selectReplica
         * @param replica_id
         * @param cb
         */
        select: function (replica_id, cb) {
            SKApp.server.api('dialog/get', {
                'dialogId': replica_id,
                'time':     SKApp.simulation.getGameTime()
            }, function (data) {
                if (data.result === 1) {
                    if (cb) {
                        cb(data);
                    }
                    SKApp.simulation.parseNewEvents(data.events);
                }
            });
        },

        /**
         * @method
         * @return {'completed'|'in progress'|'waiting'}
         */
        getStatus: function () {
            return this.status || 'waiting';
        },

        /**
         * @method
         * @param {'completed'|'in progress'|'waiting'} status
         */
        setStatus: function (status) {
            //console.log('[SKEvent] Event ' + status + ' ' + this.cid);
            /**
             * @private
             * @type {"completed"|"in progress"|"waiting"}
             */
            var prev_status = this.status;
            this.status = status;
            if (prev_status !== this.status && this.status === 'in progress') {
                this.collection.trigger('event:' + this.getTypeSlug() + ':in_progress', this);
                this.trigger('in progress');
            }
            if (this.status === 'completed') {
                this.trigger('complete');
            }
        },

        /**
         * @method
         * @param cb
         */
        ignore: function (cb) {
            if (this.getTypeSlug() !== 'phone') {
                throw 'You can ignore only phone calls';
            }

            var dialogId = this.get('data')[2].id;
            // @todo: здесь сложно накручено но надо развязать
            // и перенести игнорирование во вьюху SKPhoneCallView
            SKApp.server.api('dialog/get', {
                'dialogId': dialogId,
                'time':     SKApp.simulation.getGameTime()
            }, function (data) {
                SKApp.simulation.parseNewEvents(data.events);
                if (cb !== undefined) {
                    cb();
                }
            });
        },

        /**
         * Selects replica in dialog
         *
         * @method
         * @param replica_id
         * @param cb
         */
        selectReplica: function (replica_id, cb) {
            var me = this;
            SKApp.server.api('dialog/get', {
                'dialogId': replica_id,
                'time':     SKApp.simulation.getGameTime()
            }, function (data) {
                if (data.result === 1) {
                    cb();
                    if (me.getStatus() !== 'completed') {
                        me.complete();
                    }
                    SKApp.simulation.parseNewEvents(data.events);
                    SKApp.simulation.getNewEvents();
                }
            });
        },

        /**
         * Marks event as completed
         *
         * @method
         */
        'complete': function () {
            if (this.getStatus() === 'completed') {
                throw 'This event is already completed';
            }
            this.setStatus('completed');
        }
    });
    return SKEvent;
});