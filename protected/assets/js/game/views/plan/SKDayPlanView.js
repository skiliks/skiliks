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
            'dblclick .day-plan-todo-task':'doSetTask'
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
            this.overflowText(drop_td, max_height, drop_td.find('.title'));
            // Hiding next N cells
            var currentRow = drop_td.parents('tr');
            for (var i = 0; i < duration - 15; i += 15) {
                currentRow = currentRow.next();
                currentRow.find('.planner-book-timetable-event-fl, .planner-book-timetable-afterv-fl').hide();
            }
            drop_td.attr('rowspan', duration / 15);
            // Updating draggable element list
            this.setupDraggable();
        },
        removeTodoTask:function (model) {
            this.$('.plan-todo div[data-task-id=' + model.id + ']').remove();
        },
        canContainTask:function (el, duration) {
            var res = true;
            var currentRow = el.parents('tr');
            for (var i = 0; i < duration; i += 15) {
                if (!(currentRow.find('.planner-book-timetable-event-fl').is(':visible') &&
                    currentRow.find('.day-plan-td-slot').is(':visible')
                    )) {
                    res = false;
                    break;
                }
                currentRow = currentRow.next();
            }
            return res;
        },
        setupDroppable:function () {
            var me = this;
            this.$('.day-plan-td-slot').droppable({
                hoverClass:"drop-hover",
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

                },
                /**
                 * Returns true if draggable can be dropped on the element
                 *
                 * @param draggable
                 * @return {Boolean}
                 */
                accept:function (draggable) {
                    if ($(this).parents('.planner-book-afterv-table').length) {
                        return true;
                    }
                    var duration = parseInt(draggable.attr('data-task-duration'), 10);
                    return me.canContainTask($(this),duration);
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
                if (me.canContainTask($(this), duration)) {
                    task.set('day', $(this).parents('div[data-day-id]').attr('data-day-id'));
                    task.set('date', $(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute'));
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
        }
    });
})();
