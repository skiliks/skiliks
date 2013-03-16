/*global _, Backbone, session, SKApplicationView:true, SKApp, SKLoginView, SKSimulationStartView*/

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
            me.frame = new SKSimulationView();
            me.frame.render();
            SKApp.server.on('server:error', function () {
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
            );
            this.render();
        }
    });

    return SKApplicationView;
});