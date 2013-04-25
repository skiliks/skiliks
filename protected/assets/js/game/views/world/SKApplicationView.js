/*global _, Backbone, session, SKApplicationView:true, SKApp, SKLoginView, SKSimulationStartView,
 SKSimulationView, define, Bot */

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
            var me = this;
            SKApp.simulation.start();
            SKApp.simulation.on('user-agree-with-sim-stop', function () {
                delete me.simulation_view;
                location.href = this.get('result-url');
            });
            SKApp.simulation.on('stop', function () {
                // after 20:00 - wait for user confirmation by 'user-agree-with-sim-stop'
                if (SKApp.simulation.getGameMinutes() < SKApp.simulation.timeStringToMinutes(SKApp.get('finish'))) {
                    delete me.simulation_view;
                    location.href = this.get('result-url');
                }
            });
            me.frame = new SKSimulationView();
            me.frame.render();
            SKApp.server.on('server:error', function () {
                    if (true === SKApp.simulation.isDebug()) {
                        me.message_window = me.message_window || new window.SKDialogView({
                            'message':'Увы, произошла ошибка! Нам очень жаль и мы постараемся исправить ее как можно скорее',
                            'buttons':[
                                {
                                    'value':'Окей',
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
            this.render();
        }
    });

    return SKApplicationView;
});