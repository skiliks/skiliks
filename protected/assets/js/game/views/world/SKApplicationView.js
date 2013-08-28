/*global _, Backbone, session, SKApplicationView:true, SKApp, SKLoginView, SKSimulationStartView,
 SKSimulationView, define, Bot, $ */

var SKApplicationView;

define([
    "text!game/jst/world/simulation_template.jst",

    "game/models/SKApplication",
    "game/views/world/SKSimulationView",
    "game/views/world/SKLoginView"
], function (
    simulation_template
    ) {
    "use strict";
    /**
     * Глобальный view нашего приложения
     *
     * Слушает события авторизации и показывает SKLoginView или SKSimulationStartView в зависимости от успеха или провала
     * авторизации
     *
     * @class SKApplicationView
     * @augments Backbone.View
     */
    SKApplicationView = Backbone.View.extend({

        'el':'body',
        /**
         * Constructor
         * @method initialize
         */
        'initialize':function () {
            try {
                var me = this;

                me.frame = new SKSimulationView();

                SKApp.simulation.on('user-agree-with-sim-stop', function () {
                    delete me.simulation_view;
                    if(SKApp.isTutorial()){
                        /* Создана форма для загрузки конфига full симуляции через отправку формы и post данных */
                        var input = document.createElement('input');
                        input.setAttribute('type', 'text');
                        input.setAttribute('name', 'start');
                        input.setAttribute('value', 'full');
                        var form = document.createElement('form');
                        form.setAttribute('method', 'post');
                        form.setAttribute('action', location.href);
                        form.appendChild(input);
                        var submit = document.createElement('input');
                        input.setAttribute('type', 'submit');
                        input.setAttribute('name', 'btn');
                        input.setAttribute('value', 'yes');
                        form.appendChild(submit);
                        form.style.display = 'none';
                        document.getElementById("canvas").appendChild(form);
                        form.submit();
                        //$.post(SKApp.get('result-url'),{start:'promoFull'});
                    }else{
                        location.assign(SKApp.get('result-url'));
                    }
                });
                SKApp.simulation.on('force-stop', function () {
                    //location.assign('/dashboard');
                });
                SKApp.simulation.on('stop', function () {
                    // after 20:00 - wait for user confirmation by 'user-agree-with-sim-stop'
                    if (SKApp.simulation.getGameMinutes() < SKApp.simulation.timeStringToMinutes(SKApp.get('finish'))) {
                        delete me.simulation_view;
                        //location.assign(SKApp.get('result-url'));
                    }
                });

                SKApp.server.on('server:error', function () {
                        if (true === SKApp.simulation.isDebug()) {
                            me.message_window = me.message_window || new window.SKDialogView({
                                'message':'Увы, произошла ошибка! Нам очень жаль и мы постараемся исправить ее как можно скорее',
                                'buttons':[
                                    {
                                        'value':'Ок',
                                        'onclick':function () {
                                            delete me.message_window;
                                        }
                                    }
                                ]
                            });
                        }

                        // notify Bot
                        if ('undefined' !== typeof window.Bot) {
                            window.Bot.handle500();
                        }
                    }
                );
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        drawDesktop: function() {
            try {
                this.frame.render();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKApplicationView;
});