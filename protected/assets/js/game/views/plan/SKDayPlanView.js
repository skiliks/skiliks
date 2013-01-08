/*global Backbone, _, SKApp, SKConfig, SKWindowView*/
(function () {
    "use strict";
    window.SKDayPlanView = SKWindowView.extend({
        'el':'body .plan-container',
        'events':_.defaults({
            'click .day-plan-todo-task':'doActivateTodo'
        }, SKWindowView.prototype.events),
        'initialize':function () {
            this.render();
        },
        setupDraggable:function () {
            this.$('.day-plan-todo-task').draggable({
                containment:this.$('.planner-book'),
                stack:".planner-book",
                revert:'invalid',
                appendTo:".planner-book",
                helper:'clone',
                snap:'td.planner-book-timetable-event-fl',
                snapMode:'inner',
                snapTolerance:11,
                scroll:true,
                cursorAt: { top: 4 },
                start: function(){
                    $(this).data("startingScrollTop",$(this).parent().scrollTop());
                },
                drag: function(event,ui){
                    var st = parseInt($(this).data("startingScrollTop"));
                    ui.position.top -= $(this).parent().scrollTop() - st;
                }
            });
        },
        setupDroppable: function () {
            this.$('.day-plan-td-slot').droppable({
                hoverClass: "drop-hover",
                tolerance: "pointer",
                'drop': function (event, ui) {
                    var duration = parseInt(ui.draggable.attr('data-task-duration'), 10);
                    $(this).hide();
                    ui.draggable.addClass('regular');
                    $(this).parent().append(ui.draggable);
                    var currentRow = $(this).parents('tr');
                    for (var i = 0; i < duration - 15; i+= 15) {
                        currentRow = currentRow.next();
                        currentRow.find('.planner-book-timetable-event-fl').hide();
                    }
                    $(this).parent().attr('rowspan', duration / 15);
                },
                accept:function (draggable) {
                    var duration = parseInt(draggable.attr('data-task-duration'), 10);
                    var currentRow = $(this).parents('tr');
                    for (var i = 0; i < duration; i+= 15) {
                        if (!(currentRow.find('.planner-book-timetable-event-fl').is(':visible') &&
                            currentRow.find('.day-plan-td-slot').is(':visible')
                            )) {
                            return false;
                        }
                        currentRow = currentRow.next();
                    }
                    return true;
                }
            });
        },
        renderWindow:function (window_el) {
            var me = this;
            window_el.html(_.template($('#plan_template').html(), {}));
            window_el.find('.dayPlanTodoNum').html('(' + SKApp.user.simulation.todo_tasks.length + ')');
            SKApp.user.simulation.todo_tasks.each(function (model) {
                var todo_task = $(_.template($('#todo_task_template').html(), {task:model}));
                window_el.find('.plan-todo-wrap').append(todo_task);
            });
            this.setupDroppable();
            this.setupDraggable();

        },
        doActivateTodo:function (e) {
            $(e.currentTarget).toggleClass('.day-plan-task-active');
        }
    });
})();
