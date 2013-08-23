/*global _, SKWindowView, SKDialogWindow, SKApp, console, define, $  */

var SKPhoneDialogView;

define([
    "text!game/jst/phone/dialog_template.jst",

    "game/views/SKWindowView"
], function (
    dialog_template
) {

    "use strict";
    /**
     * @class SKPhoneDialogView
     * @augments Backbone.View
     */
    SKPhoneDialogView = SKWindowView.extend({

        isDisplaySettingsButton:true,
        
        title: "Телефон",

        windowName:'phone',
        
        isDisplayCloseWindowsButton: false,
        
        isUserCanFinalizeCall: false,

        dimensions: {
            width: 872,
            height: 560
        },
        
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
         * @method
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
         * @method
         * @param window_el
         */
        renderContent:function (window_el) {
            try {
                var event = this.options.model_instance.get('sim_event'),
                    me = this,
                    my_replicas = event.getMyReplicas(),
                    remote_replica = event.getRemoteReplica();

                // if several replics come from server - hide FinalizeCallButton
                // else display FinalizeCallButton
                // this.isUserCanFinalizeCall = false by default
                //event.get('data').length < 2
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

                var callInHtml = _.template(dialog_template, {
                    'remote_replica':            remote_replica,
                    'my_replicas':               my_replicas,
                    'audio_src':                 event.getAudioSrc(),
                    'type':                      'audio/wav',
                    isUserCanFinalizeCall: this.isUserCanFinalizeCall,
                    isDisplaySettingsButton:this.isDisplaySettingsButton,
                    windowName:this.windowName
                });

                window_el.html(callInHtml);

                this.$('audio').on('ended', function(){
                    if (my_replicas.length === 0) {
                        event.selectReplica(remote_replica.id, function () {
                            me.options.model_instance.setLastDialog(remote_replica.id);
                            if (remote_replica.is_final_replica === "1") {
                                me.options.model_instance.setOnTop();
                                me.options.model_instance.close();
                            }
                        });
                    }  else if (!SKApp.simulation.isDebug()) {
                        window_el.find('.phone-reply-h').removeClass('hidden');
                    }
                });

                if (null !== remote_replica.duration && undefined !== remote_replica.duration) {
                    console.log('duration', remote_replica.duration);
                    var duration = parseInt(remote_replica.duration, 0)*1000;
                    setTimeout(function(){
                        console.log("display replicas");
                        if (my_replicas.length === 0) {
                            event.selectReplica(remote_replica.id, function () {
                                me.options.model_instance.setLastDialog(remote_replica.id);
                                if (remote_replica.is_final_replica === "1") {
                                    me.options.model_instance.setOnTop();
                                    me.options.model_instance.close();
                                }
                            });
                        }  else if (!SKApp.simulation.isDebug()) {
                            window_el.find('.phone-reply-h').removeClass('hidden');
                        }
                    }, duration);
                }else{
                    try {
                        throw new Error("duration is "+remote_replica.duration+" by sim_id = "+SKApp.simulation.id+" and code = "+remote_replica.code);
                    } catch(exception) {
                        if (window.Raven) {
                            window.Raven.captureMessage(exception.message + ',' + exception.stack);
                        }
                    }
                }

                if (0 === this.$('audio').length) {
                    window_el.find('.phone-reply-h').removeClass('hidden');
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param event
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
         * @method
         * @param e
         */
        doSelectReplica:function (e) {
            try {
                var me = this;
                e.preventDefault();
                if("true" !== $(e.currentTarget).attr('data-disabled')) {
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
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKPhoneDialogView;
});
