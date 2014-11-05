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
                        throw new Error ('Incorrect subtype ' + first_replica.dialog_subtype);
                    }
                } else if (event_types[this.get('type')] === undefined) {
                    throw new Error ('Unknown event type: ' + this.get('type'));
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
                    try {
                        if (replica.ch_to === '1') {
                            remote_replica = replica;
                        }
                    } catch(exception) {
                        if (window.Raven) {
                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                        }
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
                    try {
                        if (replica.ch_to !== '1') {
                            my_replicas.push(replica);
                        }
                    } catch(exception) {
                        if (window.Raven) {
                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                        }
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
                return SKApp.simulation.getPathForMedia(this.get('data'), 'webm');
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
                return SKApp.simulation.getPathForMedia(this.get('data'), 'jpeg');
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Возвращает картинку для <video poster="xxx">
         * @param string|undefined video_src
         * @returns string|null
         */
        getPosterSrc: function (video_src) {
            try {
                if ('undefined' == typeof video_src) {
                    return null;
                }
                if($.browser['msie'] == true || $.browser['safari']) {
                    return video_src.replaceAll('mp4', 'jpeg');
                }else{
                    return video_src.replaceAll('webm', 'jpeg');
                }
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
                return SKApp.simulation.getPathForMedia(this.get('data'), 'wav');
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
//        select: function (replica_id, cb) {
//            try {
//
//                //console.log(replica_id);
//                var dialogHistory = SKApp.simulation.dialogsHistory.where({'replica_id': replica_id});
//                console.log(dialogHistory);
//                if (1 == dialogHistory.length) {
//                    dialogHistory[0].set('is_sent', true);
//                }
//
//                SKApp.server.api('dialog/get', {
//                    'dialogId': replica_id
//                }, function (data) {
//                    try {
//                        if (data.result === 1) {
//                            if (cb) {
//                                cb(data);
//                            }
//                            SKApp.simulation.parseNewEvents(data.events, 'dialog/get');
//                        }
//                    } catch(exception) {
//                        if (window.Raven) {
//                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
//                        }
//                    }
//                });
//            } catch(exception) {
//                if (window.Raven) {
//                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
//                }
//            }
//        },

        /**
         * @method
         * @return {'completed'|'in progress'|'waiting'}
         */
        getStatus: function () {
            try {
                return this.status || 'waiting';
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param {'completed'|'in progress'|'waiting'} status
         */
        setStatus: function (status) {
            /**
             * @private
             * @type {"completed"|"in progress"|"waiting"}
             */
            try {
                var prev_status = this.status;
                this.status = status;
                if (prev_status !== this.status && this.status === 'in progress') {
                    this.collection.trigger('event:' + this.getTypeSlug() + ':in_progress', this);
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
                    throw new Error ('You can ignore only phone calls');
                }

                var dialogId = this.get('data')[2].id;
                // @todo: здесь сложно накручено но надо развязать
                // и перенести игнорирование во вьюху SKPhoneCallView

                var dialogHistory = SKApp.simulation.dialogsHistory.where();
                if (1 == dialogHistory.length) {
                    dialogHistory[0].set('is_sent', true);
                }

                SKApp.server.api('dialog/get', {
                    'dialogId': dialogId
                }, function (data) {
                    try {
                        SKApp.simulation.parseNewEvents(data.events, 'dialog/get');
                        if (cb !== undefined) {
                            cb();
                        }
                    } catch(exception) {
                        if (window.Raven) {
                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                        }
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

                var dialogHistory = SKApp.simulation.dialogsHistory.where({'replica_id': replica_id});
                if (1 == dialogHistory.length) {
                    dialogHistory[0].set('is_sent', true);
                }

                SKApp.server.api('dialog/get', {
                    'dialogId': replica_id
                }, function (data) {
                    try {
                        if (data.result === 1) {
                            if (me.getStatus() !== 'completed') {
                                me.complete();
                                cb();
                            }
                            SKApp.simulation.parseNewEvents(data.events, 'dialog/get');
                            SKApp.simulation.getNewEvents();
                        }
                    } catch(exception) {
                        if (window.Raven) {
                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                        }
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
                    throw new Error ('This event is already completed');
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