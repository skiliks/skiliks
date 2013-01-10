var SKDayPlanView;
/*global Backbone, _, SKApp, SKConfig, SKWindowView, Hyphenator*/
(function () {
    "use strict";

    /**
     * @type {SKDayPlanView}
     */
    SKDayPlanView = SKWindowView.extend({
        'el':'body .plan-container',
        'events':_.defaults({
            'click .day-plan-todo-task':'doActivateTodo',
            'dblclick .day-plan-todo-task':'doSetTask',
            'click .todo-min':'doMinimizeTodo',
            'click .todo-max':'doMaximizeTodo'
        }, SKWindowView.prototype.events),
        'initialize':function () {
            this.render();
        },
        setupDraggable:function () {
            var elements = this.$('.planner-task:not(.locked)');
            elements.draggable("destroy");
            elements.draggable({
                containment:this.$('.planner-book'),
                stack:".planner-book",
                revert:'invalid',
                appendTo:".planner-book",
                helper:'clone',
                snap:'td.planner-book-timetable-event-fl',
                snapMode:'inner',
                snapTolerance:11,
                scroll:true,
                cursorAt:{ top:4 },
                start:function () {
                    $(this).data("startingScrollTop", $(this).parent().scrollTop());
                },
                drag:function (event, ui) {
                    var st = parseInt($(this).data("startingScrollTop"), 10);
                    ui.position.top -= $(this).parent().scrollTop() - st;
                }
            });
        },
        /**
         * Stripping text until it fits in specific height
         * @param el
         * @param max_height
         * @param text_el
         */
        overflowText:function (el, max_height, text_el) {
            var j = 0;
            while (el.height() > max_height) {
                // Debug (eliminate hang up)
                j++;
                if (j > 100) {
                    break;
                }
                text_el.text(
                    text_el.text()
                        .replace(/...$/, '')
                        .split(' ')
                        .slice(0, -1)
                        .join(' ')
                        .replace(/[\.,-\/#!$%\^&\*;:{}=\-_`~()]?$/, '...')
                );
            }
        },
        removeDayPlanTask:function (task) {
            var task_el = this.$('div[data-task-id=' + task.id + ']');
            var duration = parseInt(task_el.attr('data-task-duration'), 10);
            var prev_cell = task_el.parents('td');
            prev_cell.height(11);
            prev_cell.find('.day-plan-td-slot')
                .show();
            prev_cell
                .attr('rowspan', 1);
            var prevRow = task_el.parents('tr');
            for (var j = 0; j < duration - 15; j += 15) {
                prevRow = prevRow.next();
                prevRow
                    .find('.planner-book-timetable-event-fl, .planner-book-timetable-afterv-fl')
                    .show();
            }
            this.setupDroppable();
            task_el.remove();
        },
        addDayPlanTask:function (model) {
            var duration = parseInt(model.get('duration'), 10);

            var hour = model.get('date').split(':')[0];
            var minute = model.get('date').split(':')[1];
            var drop_td = this.$('div[data-day-id=' + model.get('day') + '] td[data-hour=' + hour + '][data-minute=' + minute + ']');
            drop_td.find('.day-plan-td-slot').hide();
            drop_td.append(_.template($('#todo_task_template').html(), {task:model, type:'regular'}));
            if (model.get("type") === "2") {
                drop_td.find('.planner-task').addClass('locked');
            }
            var max_height = Math.ceil(duration / 15) * 10;
            this.overflowText(drop_td.find('.title'), max_height, drop_td.find('.title'));
            // Hiding next N cells
            var currentRow = drop_td.parents('tr');
            for (var i = 0; i < duration - 15; i += 15) {
                currentRow = currentRow.next();
                currentRow.find('.planner-book-timetable-event-fl, .planner-book-timetable-afterv-fl').hide();
            }
            drop_td.find('.day-plan-todo-task').height(Math.ceil(duration / 15) * 11);
            drop_td.height(Math.ceil(duration / 15) * 11);
            drop_td.attr('rowspan', duration / 15);
            // Updating draggable element list
            this.setupDraggable();
        },
        removeTodoTask:function (model) {
            this.$('.plan-todo div[data-task-id=' + model.id + ']').remove();
        },
        setupDroppable:function () {
            var me = this;
            var td_slot = this.$('.planner-book-today .day-plan-td-slot, .planner-book-tomorrow  .day-plan-td-slot');
            td_slot.droppable("destroy");
            td_slot.droppable({
                tolerance:"pointer",
                'drop':function (event, ui) {

                    // Reverting old element location
                    var task_id = ui.draggable.attr('data-task-id');
                    var prev_cell = ui.draggable.parents('td');
                    if (prev_cell.length) {
                        SKApp.user.simulation.dayplan_tasks.get(task_id).destroy();
                    }

                    if (ui.draggable.parents('.plan-todo').length) {
                        SKApp.user.simulation.todo_tasks.get(task_id).destroy();
                    }

                    //Appending to new location
                    SKApp.user.simulation.dayplan_tasks.create({
                        title:ui.draggable.find('.title').text(),
                        date:$(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute'),
                        task_id:task_id,
                        duration:ui.draggable.attr('data-task-duration'),
                        day:$(this).parents('div[data-day-id]').attr('data-day-id')
                    });
                    me.$('.drop-hover').removeClass('drop-hover');

                },
                over:function (event, ui) {
                    me.$('td.planner-book-timetable-event-fl').removeClass('drop-hover');
                    var currentRow = $(this).parents('tr');
                    var duration = parseInt(ui.draggable.attr('data-task-duration'), 10);
                    for (var i = 0; i < duration; i += 15) {
                        currentRow.find('td.planner-book-timetable-event-fl').addClass('drop-hover');
                        currentRow = currentRow.next();
                    }

                },
                /**
                 * Returns true if draggable can be dropped on the element
                 *
                 * @param draggable
                 * @return {Boolean}
                 */
                accept:function (draggable) {
                    var duration = parseInt(draggable.attr('data-task-duration'), 10);
                    var day = $(this).parents('div[data-day-id]').attr('data-day-id');
                    var time = $(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute');
                    var isTimeSlotFree = SKApp.user.simulation.dayplan_tasks.isTimeSlotFree(time, day, duration);
                    console.log(isTimeSlotFree + ' ' + time + ' ' + day);
                    return isTimeSlotFree;
                }
            });
            var after_vacation_slot = this.$('.planner-book-afterv-table');
            after_vacation_slot.droppable("destroy");
            after_vacation_slot.droppable({
                hoverClass:"drop-hover",
                tolerance:"pointer",
                over: function (event, ui) {
                    me.$('.drop-hover').removeClass('drop-hover');
                    me.$('.planner-book-afterv-table').addClass('drop-hover');
                },
                'drop':function (event, ui) {

                    me.$('.planner-book-after-vacation .day-plan-td-slot').each(function () {
                        var duration = ui.draggable.attr('data-task-duration');
                        var day = $(this).parents('div[data-day-id]').attr('data-day-id');
                        var time = $(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute');
                        if (SKApp.user.simulation.dayplan_tasks.isTimeSlotFree(time, day, duration)) {
                            // Reverting old element location
                            var task_id = ui.draggable.attr('data-task-id');
                            var prev_cell = ui.draggable.parents('td');
                            if (prev_cell.length) {
                                SKApp.user.simulation.dayplan_tasks.get(task_id).destroy();
                            }

                            if (ui.draggable.parents('.plan-todo').length) {
                                SKApp.user.simulation.todo_tasks.get(task_id).destroy();
                            }

                            //Appending to new location
                            SKApp.user.simulation.dayplan_tasks.create({
                                title:ui.draggable.find('.title').text(),
                                date:time,
                                task_id:task_id,
                                duration:ui.draggable.attr('data-task-duration'),
                                day:day
                            });
                            return false;
                        }
                        return true;
                    });
                }
            });
            var todo_slot = this.$('.plan-todo');
            todo_slot.droppable("destroy");

            todo_slot.droppable({
                hoverClass:"drop-hover",
                tolerance:"pointer",
                accept: function (draggable) {
                    return !draggable.parents('.plan-todo').length;
                },
                'drop':function (event, ui) {

                    // Reverting old element location
                    var task_id = ui.draggable.attr('data-task-id');
                    SKApp.user.simulation.dayplan_tasks.get(task_id).destroy();

                    //Appending to new location
                    SKApp.user.simulation.todo_tasks.create({
                        title:ui.draggable.find('.title').text(),
                        date:$(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute'),
                        id:task_id,
                        duration:ui.draggable.attr('data-task-duration'),
                        day:$(this).parents('div[data-day-id]').attr('data-day-id')
                    });

                }
            });
        },
        updateTodos:function () {
            var me = this;
            this.$('.dayPlanTodoNum').html('(' + SKApp.user.simulation.todo_tasks.length + ')');
            me.$('.plan-todo-wrap').html('');
            SKApp.user.simulation.todo_tasks.each(function (model) {
                var todo_task = $(_.template($('#todo_task_template').html(), {task:model, type:'todo'}));
                me.$('.plan-todo-wrap').append(todo_task);
            });
            this.setupDraggable();

        },

        disableOldSlots:function () {
            this.$('.planner-book-today .planner-book-timetable-event-fl').each(function () {
                var time = SKApp.user.simulation.getGameTime();
                var cell_hour = parseInt($(this).attr('data-hour'), 10);
                var current_hour = parseInt(time.split(':')[0], 10);
                var cell_minute = parseInt($(this).attr('data-minute'), 10);
                var current_minute = parseInt(time.split(':')[1], 10);
                if (cell_hour < current_hour || (cell_hour === current_hour && cell_minute < current_minute)) {
                    $(this).addClass('past-slot');
                }
            });
        },

        /**
         * Renders inner part of the window
         * @param window_el
         */
        renderWindow:function (window_el) {
            var me = this;
            window_el.html(_.template($('#plan_template').html(), {}));
            this.updateTodos();

            SKApp.user.simulation.todo_tasks.on('add remove reset', function () {
                me.updateTodos();
            });
            SKApp.user.simulation.todo_tasks.on('remove', function (model) {
                me.removeTodoTask(model);
            });
            SKApp.user.simulation.dayplan_tasks.each(function (model) {
                me.addDayPlanTask(model);
            });
            SKApp.user.simulation.dayplan_tasks.on('remove', function (model) {
                me.removeDayPlanTask(model);
            });
            SKApp.user.simulation.dayplan_tasks.on('add', function (model) {
                me.addDayPlanTask(model);
            });
            SKApp.user.simulation.on('tick', function () {
                me.disableOldSlots();
            });
            me.$('.planner-book-timetable,.planner-book-afterv-table').mCustomScrollbar();

            this.setupDroppable();
            Hyphenator.run();

        },
        doActivateTodo:function (e) {
            var has_class = $(e.currentTarget).hasClass('day-plan-task-active');
            this.$('.day-plan-task-active').removeClass('day-plan-task-active');
            if (has_class) {
                $(e.currentTarget).removeClass('day-plan-task-active');
            } else {
                $(e.currentTarget).addClass('day-plan-task-active');
            }
        },
        doSetTask:function (e) {
            var me = this;
            var task_id = $(e.currentTarget).attr('data-task-id');
            var task = SKApp.user.simulation.todo_tasks.get(task_id);
            me.$('.day-plan-td-slot').each(function () {
                var duration = task.get('duration');
                task.set('day', $(this).parents('div[data-day-id]').attr('data-day-id'));
                task.set('date', $(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute'));
                if (SKApp.user.simulation.dayplan_tasks.isTimeSlotFree(task.get('date'), task.get('day'), duration)) {
                    task.destroy();
                    SKApp.user.simulation.dayplan_tasks.create({
                        title:$(e.currentTarget).find('.title').text(),
                        date:task.get('date'),
                        task_id:task.id,
                        duration:duration,
                        day:task.get('day')
                    });
                    return false;
                }
                return true;
            });
        },
        doMinimizeTodo:function () {
            this.$('.plan-todo').removeClass('open').addClass('closed');
            this.$('.planner-book-afterv-table').removeClass('half').addClass('full');
        },
        doMaximizeTodo:function () {
            this.$('.plan-todo').removeClass('closed').addClass('open');
            this.$('.planner-book-afterv-table').removeClass('full').addClass('half');
        }
    });
})();
