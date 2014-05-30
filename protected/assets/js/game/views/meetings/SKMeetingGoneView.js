/* global define, $, _, SKApp, AppView */

/**
 * @class SKMeetingGoneView
 * @augments Backbone.View
 */
var SKMeetingGoneView;

define([
    'game/views/SKWindowView',

    'text!game/jst/meeting/gone.jst'
], function (
        SKWindowView,

        meeting_gone_tpl
    ) {
    "use strict";

    SKMeetingGoneView = SKWindowView.extend({

        /**
         * События DOM на которые должна реагировать данная view
         * @var Array events
         */
        'events':_.defaults({
            'click .proceed-btn': 'doProceedWork'

            }, SKWindowView.prototype.events),

        /**
         * Стандартный родительский метод
         */
        isDisplaySettingsButton: false,

        /**
         * Стандартный родительский метод
         */
        isDisplayCloseWindowsButton: false,

        /**
         * Стандартный родительский метод
         */
        title: 'Системное сообщение',

        /**
         * Стандартный родительский метод
         */
        dimensions: {
            width: 600,
            height: 270
        },

        /**
         * Конструктор
         */
        initialize: function() {
            try {
                this.listenTo(this.options.model_instance, 'close', function() {
                    AppView.frame._hidePausedScreen();
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
         *
         * @param {jQuery} el
         */
        renderContent: function ($el) {
            try {
                var subject = this.options.model_instance.get('subject'),
                    time;

                var duration = subject.get('duration');
                var buttonLabel = 'Продолжить работу';

                if ('Пойти домой' == subject.get('label')) {
                    duration = 0;
                    buttonLabel = 'Продолжить';
                }

                time = SKApp.simulation.getGameMinutes() + parseInt(duration, 10);
                time = Math.floor(time / 60) + ':' + (time % 60 < 10 ? '0' : '') + time % 60;

                if ('Пойти домой' == subject.get('label')) {
                    time = '';
                }

                $el.html(_.template(meeting_gone_tpl, {
                    'subject': subject,
                    'returnTime': time,
                    'buttonLabel': buttonLabel
                }));

                AppView.frame._showPausedScreen();
                this.$el.css('zIndex', 1001);

                this.delegateEvents();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Стандартный родительский метод
         */
        render: function () {
            try {
                var me = this;
                this.listenTo(this.options.model_instance, 'close', function () {
                    me.remove();
                });
                me.resize();
                me.renderWindow(me.$el);

                this.resize();

                this.onResize = _.bind(this.onResize, me);
                $(window).on('resize', this.onResize);

                this.center();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Обработка клика "Вернуться со встречи"
         *
         * @param OnClickEvent e
         */
        doProceedWork: function(e) {
            try {
                var simulation = SKApp.simulation,
                    subject = this.options.model_instance.get('subject'),
                    me = this;

                simulation.stopPause(function() {

                    var duration = subject.get('duration');

                    if ('Пойти домой' == subject.get('label')) {
                        duration = 0;
                        window.AppView.frame.stopSimulation();
                    }

                    simulation.skipped_seconds += (duration * 60) / SKApp.get('skiliksSpeedFactor');
                    simulation.trigger('tick');
                    me.options.model_instance.close();
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        }
    });

    return SKMeetingView;
});