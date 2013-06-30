/*global Backbone, _, SKDialogView, SKApp, SKMailTask */

var SKMailAddToPlanDialog;

define([
    "text!game/jst/mail/add_to_plan_item.jst",
    "text!game/jst/mail/add_to_plan_pop_up.jst",
    "game/views/SKDialogView",
    "game/models/SKMailTask"
], function (
    add_to_plan_item,
    add_to_plan_pop_up
    ) {
    "use strict";

    /**
     * @class SKMailAddToPlanDialog
     * @augments Backbone.View
     */
    SKMailAddToPlanDialog = SKDialogView.extend({
        /**
         * Used to add reverce link from view to it`s model
         * @param mailClient SKMailClient
         */
        mailClient:undefined,

        selectedMailTask:undefined,

        isCloseWhenClickNotOnDialog:true,

        events:{
            'click #MailClient_AddToPlanPopUp .mail-plan-btn' : 'doAddToPlan',
            'click .mail-plan-item':                            'doSelectItem'
        },

        /**
         * Constructor
         * @method initialize
         */
        initialize:function () {
            var me = this;

            this.listenTo(this, 'mail:close', function () {
                me.close();
            });
            this.listenTo(this, '.click-prevent-click-element', function () {
                me.doLogClose();
            });
        },

        /**
         * @method
         * @returns {$.xhr}
         */
        getTasksToBePlanned:function () {
            var me = this;

            return SKApp.server.api(
                'mail/toPlan',
                {
                    id: SKApp.simulation.mailClient.activeEmail.mySqlId
                },
                function (response) {
                    SKApp.simulation.mailClient.availaleActiveEmailTasks = [];
                    response.data.forEach(function (task_obj) {
                        var task = new SKMailTask();
                        task.mySqlId = task_obj.id;
                        task.label = task_obj.name;
                        task.duration = task_obj.duration;

                        SKApp.simulation.mailClient.availaleActiveEmailTasks.push(task);
                    });

                    me.continueRender();
                }
            );
        },

        /**
         * @method
         */
        render:function () {
            SKApp.simulation.mailClient.setWindowsLog('mailPlan', SKApp.simulation.mailClient.getActiveEmailId());

            // generate mail tasks list {
            this.getTasksToBePlanned();
        },

        /**
         * @method
         */
        continueRender: function() {
            var listHtml = '';
            var addToPlanDialog = this;

            var mailTasks = SKApp.simulation.mailClient.availaleActiveEmailTasks; // to keep code shorter

            mailTasks.forEach(function (mailTask) {
                listHtml += _.template(add_to_plan_item, {
                    id:      mailTask.mySqlId,
                    text:    mailTask.label,
                    duration:mailTask.getFormatedDuration()
                });
            });
            // generate mail tasks list }

            if (0 === mailTasks.length) {
                addToPlanDialog.message_window = new SKDialogView({
                    'message':'Это письмо уже запланировано.',
                    'buttons':[
                        {
                            'value':'Ок',
                            'onclick':function () {
                                delete SKApp.simulation.mailClient.message_window;
                                SKApp.simulation.mailClient.setWindowsLog(
                                    'mailMain',
                                    SKApp.simulation.mailClient.getActiveEmailId()
                                );
                            }
                        }
                    ]
                });

                return;
            }

            var me = this;

            // preventOtherClicks
            me.renderPreventClickElement();

            // render dialog {
            var dialogHtml = _.template(add_to_plan_pop_up, {
                list:listHtml,
                buttonLabel:'Запланировать'
            });

            this.$el = $(dialogHtml);

            this.$el.topZIndex();

            this.$el.css({
                'left':$(".mail-window .ADD_TO_PLAN").offset().left + 'px',
                'top':'70px',
                'position':'absolute',
                'width':'100%',
                'margin':'auto'
            });

            $('.windows-container').prepend(this.$el);
            // render dialog }

            this.delegateEvents();
        },

        /**
         * override default behavoiur
         * @method
         */
        handleClick:function () {
        },

        /**
         * @method
         * @param id
         */
        selectItem:function (id) {
            $('.mail-plan-item').removeClass('active');
            $('.mail-task-' + id).addClass('active');

            this.setSelectedMailTaskByMySqlId(id);
        },

        /**
         * @method
         * @param id
         */
        setSelectedMailTaskByMySqlId:function (id) {
            this.selectedMailTask = SKApp.simulation.mailClient.getMailTaskByMySqlId(id);
        },

        /**
         * @method
         */
        doAddToPlan:function () {
            var addToPlanDialog = this;

            // check it action possible {
            if (undefined === addToPlanDialog.selectedMailTask) {
                addToPlanDialog.message_window = addToPlanDialog.message_window || new SKDialogView({
                    'message':'Задача не выбрана.',
                    'buttons':[
                        {
                            'value':'Ок',
                            'onclick':function () {
                                delete addToPlanDialog.message_window;
                            }
                        }
                    ]
                });

                return;
            }
            // check it action possible }

            addToPlanDialog.close();

            // mark email planned
            SKApp.server.api(
                'mail/addToPlan',
                {
                    id:        addToPlanDialog.selectedMailTask.mySqlId,
                    messageId: SKApp.simulation.mailClient.activeEmail.mySqlId
                },
                function (response) {
                    // add to plan {
                    SKApp.simulation.todo_tasks.fetch({update: true});
                    // add to plan }
                    SKApp.simulation.mailClient.setTaskId(addToPlanDialog.selectedMailTask.mySqlId);

                    SKApp.simulation.mailClient.setWindowsLog(
                        'mailMain',
                        SKApp.simulation.mailClient.getActiveEmailId()
                    );

                    SKApp.simulation.window_set.toggle('plan', 'plan'); // for logging
                }
            );
        },

        /**
         * @method
         * @param events
         */
        doSelectItem: function (events) {
            this.selectItem($(events.currentTarget).attr('data-task-id'));
        },

        /**
         * @method
         */
        doLogClose: function() {
            SKApp.simulation.mailClient.setWindowsLog(
                'mailMain',
                SKApp.simulation.mailClient.getActiveEmailId()
            );   
        },

        /**
         * @method
         */
        close:function () {
            if (undefined !== this.$el) {
                this.cleanUpDOM();
            }
        }
    });

    return SKMailAddToPlanDialog;
});
