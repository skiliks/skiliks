/*global Backbone, _, SKDialogView */
(function () {
    "use strict";
    window.SKMailAddToPlanDialog = SKDialogView.extend({        
        /**
         * Used to add reverce link from view to it`s model
         * @param mailClient SKMailClient
         */
        mailClient: undefined,
        
        selectedMailTask: undefined,
        
        isCloseWhenClickNotOnDialog: true,
        
        initialize: function () {
            var me = this;
            
            this.listenTo(this, 'mail:close', function () {
                me.close();
            });
        },
        
        getTasksToBePlanned: function() {
            return SKApp.server.api(
                'mail/toPlan',
                {
                    id: this.mailClient.activeEmail.mySqlId
                }, 
                function (response) {
                    SKApp.user.simulation.mailClient.availaleActiveEmailTasks = [];
                    for (var i in response.data) {
                        var task = new SKMailTask();
                        task.mySqlId  = response.data[i].id;
                        task.label    = response.data[i].name;
                        task.duration = response.data[i].duration;
                        
                        SKApp.user.simulation.mailClient.availaleActiveEmailTasks.push(task);
                    }
                },
                false
            );   
        },
        events: {
            'click #MailClient_AddToPlanPopUp .mail-plan-btn' : 'doAddToPlan'
        },
        
        render: function () { 
            var listHtml = '';
            var addToPlanDialog = this;
            
            addToPlanDialog.mailClient.setWindowsLog('mailPlan');
            
            // generate mail tasks list {
            this.getTasksToBePlanned();
            
            var mailTasks = SKApp.user.simulation.mailClient.availaleActiveEmailTasks; // to keep code shorter
            
            for (var i in mailTasks) {
                listHtml +=  _.template($('#MailClient_AddToPlanItem').html(), {
                    id:       mailTasks[i].mySqlId,
                    text:     mailTasks[i].label,
                    duration: mailTasks[i].getFormatedDuration()
                });
            }
            // generate mail tasks list }
            
            if (0 == mailTasks.length) {
                addToPlanDialog.message_window = new SKDialogView({
                    'message': 'Это письмо нельзя запланировать.',
                    'buttons': [
                        {
                            'value': 'Окей',
                            'onclick': function () {
                                delete SKApp.user.simulation.mailClient.message_window;
                                addToPlanDialog.mailClient.setWindowsLog(
                                    'mailPreview',
                                    addToPlanDialog.mailClient.getActiveEmailId()
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
                list: listHtml,
                buttonLabel: 'Запланировать'
            });
            
            this.$el = $(dialogHtml);
            
            this.$el.topZIndex();
            
            this.$el.css({
                'left' : $(".mail-window .ADD_TO_PLAN").offset().left + 'px',
                'top': '70px',
                'position': 'absolute',
                'width': '100%',
                'margin': 'auto'
            });
            
            $('#canvas').prepend(this.$el);
            // render dialog }
            
            this.delegateEvents();
        },
        
        // override default behavoiur
        handleClick: function () {},
        
        selectItem: function(id) {
            $('.mail-plan-item').removeClass('active');
            $('.mail-task-'+id).addClass('active');

            this.setSelectedMailTaskByMySqlId(id);  
        },
        
        setSelectedMailTaskByMySqlId: function(id) {
            this.selectedMailTask = this.mailClient.getMailTaskByMySqlId(id);
        },
        
        doAddToPlan: function () {
            var addToPlanDialog = this;
            
            // check it action possible {
            if (undefined === addToPlanDialog.selectedMailTask) {
                addToPlanDialog.message_window = addToPlanDialog.message_window || new SKDialogView({
                    'message': 'Задача не выбрана.',
                    'buttons': [
                        {
                            'value': 'Окей',
                            'onclick': function () {
                                delete addToPlanDialog.message_window;
                            }
                        }
                    ]
                });
                    
                return;
            }
            // check it action possible }
            
            addToPlanDialog.close();
            
            // add to plan {
            SKApp.user.simulation.todo_tasks.create({
                title:    addToPlanDialog.selectedMailTask.label,
                duration: parseInt(addToPlanDialog.selectedMailTask.duration)
            });
            // add to plan }
            
            SKApp.user.simulation.window_set.toggle('plan','plan'); // for logging
            
            // mark email planned
            SKApp.server.api(
                'mail/markPlanned',
                { 
                    emailId: addToPlanDialog.selectedMailTask.mySqlId
                }, 
                function (response) {},
                false
            ); 
        },
        
        close: function() {
            if (undefined !== this.$el) {
                this.cleanUpDOM();
            }
            
            this.mailClient.setWindowsLog(
                'mailPreview',
                this.mailClient.getActiveEmailId()
            );
        }
    });
})();
