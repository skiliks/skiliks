/*global Backbone, _, SKDialogView */
(function () {
    "use strict";
    window.SKMailAddToPlanDialog = Backbone.View.extend({
        
        DomElement:                undefined,
        
        preventOtherClicksElement: undefined,
        
        /**
         * Used to add reverce link from view to it`s model
         * @param mailClient SKMailClient
         */
        mailClient: undefined,
        
        selectedMailTask: undefined,
        
        initialize: function () {},
        
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
        
        render: function () {  
            var listHtml = '';
            
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
            
            var me = this;
            
            // preventOtherClicks {
            this.preventOtherClicksElement = 
                $('<div class="preventOtherClicks" style="position: absolute; background: none repeat scroll 0 0 transparent; height: 100%;;width:100%;"></div>');
            
            this.preventOtherClicksElement.topZIndex();
            
            $('#canvas').prepend(this.preventOtherClicksElement);
            
            $('.preventOtherClicks').click(function(){
                me.close();
            });

            // render dialog {
            var dialogHtml = _.template($('#MailClient_AddToPlanPopUp').html(), {
                list: listHtml,
                buttonLabel: 'Запланировать'
            });
            
            this.DomElement = $(dialogHtml);
            
            this.DomElement.topZIndex();
            
            this.DomElement.css({
                'left' : $("#mailEmulatorMainScreen .ADD_TO_PLAN").offset().left + 'px',
                'top': '70px',
                'position': 'absolute',
                'width': '100%',
                'margin': 'auto'
            });
            
            $('#canvas').prepend(this.DomElement);
            
            $('#MailClient_AddToPlanPopUp .mail-plan-btn').click(function(){
                me.doAddToPlan();
            });
            
            // render dialog }
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
            
            // add to plan {
            return SKApp.server.api(
                'mail/addToPlan',
                { 
                    id:        addToPlanDialog.selectedMailTask.mySqlId,
                    messageId: addToPlanDialog.mailClient.activeEmail.mySqlId
                }, 
                function (response) {
                    addToPlanDialog.close();
                    
                    if (response.result == 1) {
                        new SKDayPlanView();
                    }
                }
            );  
            // add to plan }
        },
        
        close: function() {
            if (undefined !== this.DomElement) {
                this.DomElement.remove();
                this.preventOtherClicksElement.remove();
            }
        }
    });
})();
