/*global SKVisitView:true, SKWindowView, Backbone, _, SKApp, SKConfig, SKDialogWindow*/

var SKVisitView;

define([
        'game/views/SKWindowView',

        'text!game/jst/visit/visit_door.jst'
    ], function (
        SKWindowView,

        visitDoorTpl
    ) {
    "use strict";
    /**
     * Отображение визита пользователя
     * @class SKVisitView
     * @augments Backbone.View
     */
    SKVisitView = SKWindowView.extend(
        /** @lends SKVisitView.prototype */
        {
            isDisplayCloseWindowsButton: false,
            
            'events':_.defaults({
                    "click .visitor-allow":'allow',
                    "click .visitor-deny":'deny'
            },SKWindowView.prototype.events),

            /**
             * Constructor
             * @method initialize
             */
            'initialize':function () {
                try {
                    var me = this;
                    SKWindowView.prototype.initialize.call(this);
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @method
             * @param el
             */
            'renderWindow':function (el) {
                try {
                    var me = this,
                        event = this.options.model_instance.get('sim_event');

                    el.html(_.template(visitDoorTpl, {
                        'visit' :                     event.get('data'),
                        isDisplayCloseWindowsButton : this.isDisplayCloseWindowsButton
                    }));

                    if ('undefined' === typeof event.get('data')[2]) {
                        me.timer = setTimeout(function() {
                            me.$('.visitor-allow').click();
                            me.timer = null;
                        }, 5000);
                    }
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
            'allow':function (e) {
                try {
                    var dialogId = $(e.currentTarget).attr('data-dialog-id');

                    if (this.timer) {
                        clearTimeout(this.timer);
                        this.timer = null;
                    }

                    this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {});
                    this.options.model_instance.close();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             *
             * @param e
             */
            'deny':function (e) {
                try {
                    var dialogId = $(e.currentTarget).attr('data-dialog-id');
                    SKApp.simulation.trigger('audio-door-knock-stop');
                    this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                    });
                    this.options.model_instance.close();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            }
        });
    return SKVisitView;
});