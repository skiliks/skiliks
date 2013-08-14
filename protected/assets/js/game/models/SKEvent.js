/*global SKEvent:true, Backbone, SKConfig, SKApp, _, console, define */
define([], function () {
    "use strict";
    /**
     * @property event_types
     * @type {Array of String}
     * @default {Array}
     */
    var event_types = {
        'M':  'mail',
        'MS': 'mail-send',
        'D':  'document',
        'P':  'plan',
         2:   'event'
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
         * @method getTypeSlug
         * @return {'phone'|'immediate-phone'|'immediate-visit'|'visit'}
         */
        getTypeSlug: function () {

            try {

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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }

        },

        /**
         * In dialogs returns replica of foreign character
         * @method getRemoteReplica
         * @return {SKDialogReplica}
         */
        getRemoteReplica: function () {
            try {
                var replicas = this.get('data');
                var remote_replica = null;
                replicas.forEach(function (replica) {
                    if (replica.ch_to === '1') {
                        remote_replica = replica;
                    }
                });
                return remote_replica;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Returns array of player's answers
         *
         * @method
         * @return {Array}
         */
        getMyReplicas: function () {
            try {
                var replicas = this.get('data');
                var my_replicas = [];

                replicas.forEach(function (replica) {
                    if (replica.ch_to !== '1') {
                        my_replicas.push(replica);
                    }
                });

                return _.shuffle(my_replicas);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Returns absolute UPL to video background of image
         *
         * @method
         * @return {String}
         */
        getVideoSrc: function () {
            try {
                var replicas = this.get('data');
                var video_src;
                replicas.forEach(function (replica) {
                    video_src = video_src || replica.sound;
                });
                if (null !== video_src && !video_src.match(/\.webm$/)) {
                    video_src = undefined;
                }
                return video_src ? SKApp.get('storageURL') + '/videos/' + video_src : undefined;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Returns absolute UPL to image background of image
         *
         * @method
         * @return {String}
         */
        getImgSrc: function () {
            try {
                var replicas = this.get('data');
                var img_src = null;
                replicas.forEach(function (replica) {
                    img_src = img_src || replica.sound;
                });
                if (null !== img_src && !img_src.match(/\.jpeg$/)) {
                    img_src = undefined;
                }
                return img_src ? SKApp.get('storageURL') + '/dialog_images/' + img_src : undefined;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @returns {string}
         */
        getAudioSrc: function () {
            try {
                var replicas = this.get('data');
                var audio_src = null;
                replicas.forEach(function (replica) {
                    audio_src = audio_src || replica.sound;
                });
                if (audio_src !== null && !audio_src.match(/\.wav/) && !audio_src.match(/\.ogg/)) {
                    audio_src = null;
                }
                return audio_src ? SKApp.get('storageURL') + '/sounds/' + audio_src : undefined;
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @deprecated use selectReplica
         * @param replica_id
         * @param cb
         */
        select: function (replica_id, cb) {
            try {
                SKApp.server.api('dialog/get', {
                    'dialogId': replica_id
                }, function (data) {
                    if (data.result === 1) {
                        if (cb) {
                            cb(data);
                        }
                        //console.log('parseNewEvents: ', data.events);
                        SKApp.simulation.parseNewEvents(data.events, 'dialog/get');
                    }
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
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
            try {
                var prev_status = this.status;
                this.status = status;
                if (prev_status !== this.status && this.status === 'in progress') {
                    this.collection.trigger('event:' + this.getTypeSlug() + ':in_progress', this);
                    //console.log('event:' + this.getTypeSlug() + ':in_progress');
                    this.trigger('in progress');
                }
                if (this.status === 'completed') {
                    this.trigger('complete');
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param cb
         */
        ignore: function (cb) {
            try {
                if (this.getTypeSlug() !== 'phone') {
                    throw 'You can ignore only phone calls';
                }

                var dialogId = this.get('data')[2].id;
                // @todo: здесь сложно накручено но надо развязать
                // и перенести игнорирование во вьюху SKPhoneCallView
                SKApp.server.api('dialog/get', {
                    'dialogId': dialogId
                }, function (data) {
                    // console.log('ignore: ', data.events);
                    SKApp.simulation.parseNewEvents(data.events, 'dialog/get');
                    if (cb !== undefined) {
                        cb();
                    }
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Selects replica in dialog
         *
         * @method
         * @param replica_id
         * @param cb
         */
        selectReplica: function (replica_id, cb) {
            try {
                var me = this;
                SKApp.server.api('dialog/get', {
                    'dialogId': replica_id
                }, function (data) {
                    if (data.result === 1) {
                        //console.log('selectReplica: ', data.events);
                        if (me.getStatus() !== 'completed') {
                            me.complete();
                            cb();
                        }
                        SKApp.simulation.parseNewEvents(data.events, 'dialog/get');
                        SKApp.simulation.getNewEvents();
                    }
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Marks event as completed
         *
         * @method
         */
        'complete': function () {
            try {
                if (this.getStatus() === 'completed') {
                    throw 'This event is already completed';
                }
                this.setStatus('completed');
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });
    return SKEvent;
});