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

            deny_timeout_id:null,

            /**
             * События DOM на которые должна реагировать данная view
             * @var Array events
             */
            'events':_.defaults({
                    "click .visitor-allow":'allow',
                    "click .visitor-deny":'deny'
            },SKWindowView.prototype.events),

            /**
             * Constructor
             * @method initialize
             */
            initialize:function () {
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
             * @param DomElement el
             */
            renderWindow:function (el) {
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
                    var noReply = function(){
                        me.doActivate();
                        me.$('.visitor-deny').click();
                    };
                    this.deny_timeout_id = setTimeout(noReply, 20000);
                    this.listenTo(this.options.model_instance, 'close', function () {
                        clearTimeout(me.deny_timeout_id);
                    });
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             * @param Event e, OnClickEvent
             */
            allow:function (event) {
                try {
                    var dialogId = $(event.currentTarget).attr('data-dialog-id');
                    var me = this;
                    if (this.timer) {
                        clearTimeout(this.timer);
                        this.timer = null;
                    }

                    this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                        me.options.model_instance.setLastDialog(dialogId);
                    });
                    this.options.model_instance.close();
                } catch(exception) {
                    if (window.Raven) {
                        window.Raven.captureMessage(exception.message + ',' + exception.stack);
                    }
                }
            },

            /**
             *
             * @param Event event, OnClickEvent
             */
            deny:function (e) {
                try {
                    var me = this;
                    var dialogId = $(event.currentTarget).attr('data-dialog-id');
                    SKApp.simulation.trigger('audio-door-knock-stop');
                    this.options.model_instance.get('sim_event').selectReplica(dialogId, function () {
                        me.options.model_instance.setLastDialog(dialogId);
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