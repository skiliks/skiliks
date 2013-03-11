/*global _, Backbone, session, SKApplicationView:true, SKApp, SKLoginView, SKSimulationStartView*/

var SKApplicationView;

define([
    "text!game/jst/world/simulation_template.jst",

    "game/models/SKApplication",
    "game/views/world/SKSimulationStartView",
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
            SKApp.session.on('login:failure logout', function (error_type) {
                if (me.login_view === undefined) {
                    var container = me.make('div');
                    me.$el.append(container);
                    me.login_view = new SKLoginView({el:container});
                    me.login_view.render();
                }
            });
            SKApp.session.on('login:success', function () {
                if (me.login_view !== undefined) {
                    me.login_view.remove();
                }
                me.frame = new SKSimulationStartView({'simulations':SKApp.user.simulations});
                me.frame.render();
            });
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
        },

        /**
         * При отображении запускает проверку сессии пользователя
         *
         * @method
         */
        'render':function () {
            SKApp.session.check();
        }
    });

    return SKApplicationView;
});