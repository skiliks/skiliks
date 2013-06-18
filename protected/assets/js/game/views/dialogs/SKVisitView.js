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
                var me = this;
                SKWindowView.prototype.initialize.call(this);
            },

            /**
             * @method
             * @param el
             */
            'renderWindow':function (el) {
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
            },

            /**
             * @method
             * @param e
             */
            'allow':function (e) {
                var dialogId = $(e.currentTarget).attr('data-dialog-id');

                if (this.timer) {
                    clearTimeout(this.timer);
                    this.timer = null;
                }

                this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {});
                this.options.model_instance.close();
            },

            /**
             *
             * @param e
             */
            'deny':function (e) {
                var dialogId = $(e.currentTarget).attr('data-dialog-id');
                SKApp.simulation.trigger('audio-door-knock-stop');
                this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                });
                this.options.model_instance.close();
            }
        });
    return SKVisitView;
});