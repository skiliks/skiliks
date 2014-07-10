/*global _, SKWindowView, SKDialogWindow, SKApp, console, define, $  */

var SKPhoneDialogView;

define([
    "text!game/jst/phone/dialog_template.jst",
    "game/models/SKDialogHistory",
    "game/views/SKWindowView"
], function (
    dialog_template,
    SKDialogHistory
) {

    "use strict";
    /**
     * @class SKPhoneDialogView
     * @augments Backbone.View
     */
    SKPhoneDialogView = SKWindowView.extend({

        /**
         * Стандартное родительское свойство
         */
        isDisplaySettingsButton: true,

        /**
         * Стандартное родительское свойство
         */
        title: "Телефон",

        /**
         * Стандартное родительское свойство
         */
        windowName: 'phone',

        /**
         * Стандартное родительское свойство
         */
        isDisplayCloseWindowsButton: false,

        /**
         * Определяет может ли пользователь самовольно завершить звонок
         *
         * @param Boolean isUserCanFinalizeCall
         */
        isUserCanFinalizeCall: false,

        /**
         * Стандартный родительский метод
         */
        dimensions: {
            width: 872,
            height: 560
        },

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        'events':_.defaults({
            'click .phone-draw-menu': 'getMenu',
            'click .replica-select' : 'doSelectReplica'
        }, SKWindowView.prototype.events),

        /**
         * Constructor
         * @method initialize
         */
        initialize:function() {
            try {
                SKApp.simulation.trigger('audio-phone-small-zoom-stop');
                var me = this;
                this.listenTo(this.options.model_instance, 'refresh', function () {
                    me.render();
                });
                SKWindowView.prototype.initialize.call(this);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Стандартный родительский метод
         */
        remove: function () {
            try {
                var event = this.options.model_instance.get('sim_event');
                if (event.getStatus() !== 'completed') {
                    event.complete();
                }
                this.off('dialog:end');
                SKWindowView.prototype.remove.call(this);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Стандартный родительский метод
         * @param {jQuery} el
         */
        renderContent:function (el) {
            try {
                var event = this.options.model_instance.get('sim_event'),
                    me = this,
                    my_replicas = event.getMyReplicas(),
                    remote_replica = event.getRemoteReplica();

                if (event.get('data')[0].code === 'None' || event.get('data')[0].code === 'Auto') {
                    var timeout = setTimeout(function(){
                        if(me.options.model_instance.is_opened === true){
                            SKApp.simulation.trigger('audio-phone-end-start');
                            me.options.model_instance.setOnTop();
                            me.options.model_instance.close();
                        }

                    }, 5000);
                    this.isUserCanFinalizeCall = true;
                } else {
                    this.isUserCanFinalizeCall = false;
                }

                var sound = event.getAudioSrc();

                for (var i in my_replicas) {
                    var dialogHistory = new SKDialogHistory();
                    dialogHistory.set('replica_id', my_replicas[i]['id']);
                    dialogHistory.set('is_sent', false);
                    SKApp.simulation.dialogsHistory.add(dialogHistory);
                }

                var callInHtml = _.template(dialog_template, {
                    'remote_replica':            remote_replica,
                    'my_replicas':               my_replicas,
                    'audio_src':                 sound,
                    isUserCanFinalizeCall: this.isUserCanFinalizeCall,
                    isDisplaySettingsButton:this.isDisplaySettingsButton,
                    windowName:this.windowName
                });

                el.html(callInHtml);

                    var duration = (SKApp.simulation.isDebug() || null === remote_replica) ?
                        0 : parseInt(remote_replica.duration, 0)*1000;

                    // Для дев режима, последняя реплика в диалоге, если нет вариантов ответа - сразу исчезает.
                    // Из-за этого тесты которые проверяют отображение реплик валятся
                    // 5 сек задержки должно хватать, но если не хватит можно увеличить

                    if (SKApp.simulation.isDebug() && 0 == my_replicas.length) {
                        duration = 5000;
                    }

                    setTimeout(function(){
                        if (my_replicas.length === 0) {
                            event.selectReplica(remote_replica.id, function () {
                                me.options.model_instance.setLastDialog(remote_replica.id);
                                if (remote_replica.is_final_replica === "1") {
                                    me.options.model_instance.setOnTop();
                                    me.options.model_instance.close();
                                }
                            });
                        }  else if (!SKApp.simulation.isDebug()) {
                            el.find('.phone-reply-h').removeClass('hidden');
                        }
                    }, duration);

                if (0 === this.$('audio').length) {
                    el.find('.phone-reply-h').removeClass('hidden');
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Рендер основного меню телефона
         * @param OnClickEvent event
         */
        getMenu: function(event) {
            try {
                SKApp.simulation.trigger('audio-phone-small-zoom-stop');
                // block standard functionality if user has no rights to terminate call
                if (this.isUserCanFinalizeCall) {
                    this.options.model_instance.setOnTop();
                    this.options.model_instance.close();
                    SKApp.simulation.window_set.toggle('phone','phoneMain');
                }
                event.preventDefault();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Обработка клика по реплике-ответу
         * Блокирование всех реплик после первого клика
         *
         * @param OnClickEvent e
         */
        doSelectReplica:function (e) {
            try {
                var me = this;
                e.preventDefault();
                if("true" !== $(e.currentTarget).attr('data-disabled')) {

                    //Блокирование всех реплик после первого клика
                    $('#phoneAnswers li').each(function(index, element) {
                        $(element).find('a').attr('data-disabled', 'true');
                    });

                    var event = this.options.model_instance.get('sim_event');
                    var dialog_id = $(e.currentTarget).attr('data-id');
                    var is_final = $(e.currentTarget).attr('data-is-final');
                    event.selectReplica(dialog_id, function () {
                        me.options.model_instance.setLastDialog(dialog_id);
                        /* TODO refactor */
                        if (is_final) {
                            SKApp.simulation.trigger('audio-phone-end-start');
                            me.options.model_instance.setOnTop();
                            me.options.model_instance.close();
                        }
                    });

                    var ids = new Array();
                    $('.replica-select').each(function(i){
                        ids[i] = $(this).attr('data-id');
                    });

                    setTimeout(_.bind(me.restoreReplicasAccessibility, me), 60*1000, ids); // 1 min
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        restoreReplicasAccessibility: function(ids)
        {
            console.log('restoreReplicasAccessibility', ids);
            var me = this;

            var isNoOneReplicaSent = true;
            for (var i in ids) {
                var id = ids[i];
                var dialogHistory = SKApp.simulation.dialogsHistory.where({'replica_id': id, 'is_sent': true});
                if (0 < dialogHistory.length) {
                    isNoOneReplicaSent = false;
                }
            }

            console.log('isNoOneReplicaSent ', isNoOneReplicaSent);
            if (isNoOneReplicaSent) {
                $('.replica-select').removeAttr('data-disabled');
                me.options.model_instance.setOnTop();
            }
        }
    });

    return SKPhoneDialogView;
});
