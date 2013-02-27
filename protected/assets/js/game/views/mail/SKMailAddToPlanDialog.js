/*global Backbone, _, SKDialogView, SKApp, SKMailTask */

var SKMailAddToPlanDialog;

define(["game/views/SKDialogView", "game/models/SKMailTask"], function () {
    "use strict";

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

        initialize:function () {
            var me = this;

            this.listenTo(this, 'mail:close', function () {
                me.close();
            });
            this.listenTo(this, '.click-prevent-click-element', function () {
                me.doLogClose();
            });
        },

        getTasksToBePlanned:function () {
            var me = this;

            return SKApp.server.api(
                'mail/toPlan',
                {
                    id: SKApp.user.simulation.mailClient.activeEmail.mySqlId
                },
                function (response) {
                    SKApp.user.simulation.mailClient.availaleActiveEmailTasks = [];
                    response.data.forEach(function (task_obj) {
                        var task = new SKMailTask();
                        task.mySqlId = task_obj.id;
                        task.label = task_obj.name;
                        task.duration = task_obj.duration;

                        SKApp.user.simulation.mailClient.availaleActiveEmailTasks.push(task);
                    });

                    me.continueRender();
                }
            );
        },

        render:function () {
            SKApp.user.simulation.mailClient.setWindowsLog('mailPlan', SKApp.user.simulation.mailClient.getActiveEmailId());

            // generate mail tasks list {
            this.getTasksToBePlanned();
        },

        continueRender: function() {
            var listHtml = '';
            var addToPlanDialog = this;

            var mailTasks = SKApp.user.simulation.mailClient.availaleActiveEmailTasks; // to keep code shorter

            mailTasks.forEach(function (mailTask) {
                listHtml += _.template($('#MailClient_AddToPlanItem').html(), {
                    id:mailTask.mySqlId,
                    text:mailTask.label,
                    duration:mailTask.getFormatedDuration()
                });
            });
            // generate mail tasks list }

            if (0 === mailTasks.length) {
                addToPlanDialog.message_window = new SKDialogView({
                    'message':'Это письмо уже запланировано.',
                    'buttons':[
                        {
                            'value':'Окей',
                            'onclick':function () {
                                delete SKApp.user.simulation.mailClient.message_window;
                                SKApp.user.simulation.mailClient.setWindowsLog(
                                    'mailMain',
                                    SKApp.user.simulation.mailClient.getActiveEmailId()
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
            var dialogHtml = _.template($('#MailClient_AddToPlanPopUp').html(), {
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

            $('#canvas').prepend(this.$el);
            // render dialog }

            this.delegateEvents();
        },

        // override default behavoiur
        handleClick:function () {
        },

        selectItem:function (id) {
            $('.mail-plan-item').removeClass('active');
            $('.mail-task-' + id).addClass('active');

            this.setSelectedMailTaskByMySqlId(id);
        },

        setSelectedMailTaskByMySqlId:function (id) {
            this.selectedMailTask = SKApp.user.simulation.mailClient.getMailTaskByMySqlId(id);
        },

        doAddToPlan:function () {
            var addToPlanDialog = this;

            // check it action possible {
            if (undefined === addToPlanDialog.selectedMailTask) {
                addToPlanDialog.message_window = addToPlanDialog.message_window || new SKDialogView({
                    'message':'Задача не выбрана.',
                    'buttons':[
                        {
                            'value':'Окей',
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
                    messageId: SKApp.user.simulation.mailClient.activeEmail.mySqlId
                },
                function (response) {
                    // add to plan {
                    SKApp.user.simulation.todo_tasks.fetch();
                    // add to plan }

                    SKApp.user.simulation.mailClient.setWindowsLog(
                        'mailMain',
                        SKApp.user.simulation.mailClient.getActiveEmailId()
                    );

                    SKApp.user.simulation.window_set.toggle('plan', 'plan'); // for logging
                }
            );
        },

        doSelectItem: function (events) {
            this.selectItem($(events.currentTarget).attr('data-task-id'));
        },
        
        doLogClose: function() {
            SKApp.user.simulation.mailClient.setWindowsLog(
                'mailMain',
                SKApp.user.simulation.mailClient.getActiveEmailId()
            );   
        },

        close:function () {
            if (undefined !== this.$el) {
                this.cleanUpDOM();
            }
        }
    });

    return SKMailAddToPlanDialog;
});
