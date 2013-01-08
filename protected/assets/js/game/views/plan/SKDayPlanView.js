/*global Backbone, _, SKApp, SKConfig, SKWindowView, Hyphenator*/
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
            this.$('.day-plan-todo-task').draggable("destroy");
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
                        .replace(/[\.,-\/#!$%\^&\*;:{}=\-_`~()]?$/,'...')
                );
            }
        },
        setupDroppable:function () {
            var me = this;
            this.$('.day-plan-td-slot').droppable({
                hoverClass:"drop-hover",
                tolerance:"pointer",
                'drop':function (event, ui) {
                    var duration = parseInt(ui.draggable.attr('data-task-duration'), 10);
                    $(this).hide();
                    ui.draggable.addClass('regular');

                    // Reverting old element location
                    ui.draggable.parents('td').find('.day-plan-td-slot')
                        .show();
                    ui.draggable.parents('td')
                        .attr('rowspan', 1);
                    var prevRow = ui.draggable.parents('tr');
                    for (var j = 0; j < duration - 15; j += 15) {
                        prevRow = prevRow.next();
                        prevRow
                            .find('.planner-book-timetable-event-fl, .planner-book-timetable-afterv-fl')
                            .show();
                    }

                    //Appendng to new location
                    $(this).parent().append(ui.draggable);

                    var max_height = Math.ceil(duration / 15) * 10;
                    me.overflowText($(this).parent(), max_height, $(this).parent().find('.title'));
                    // Hiding next N cells
                    var currentRow = $(this).parents('tr');
                    for (var i = 0; i < duration - 15; i += 15) {
                        currentRow = currentRow.next();
                        currentRow.find('.planner-book-timetable-event-fl, .planner-book-timetable-afterv-fl').hide();
                    }
                    $(this).parent().attr('rowspan', duration / 15);
                    // Updating draggable element list
                    me.setupDroppable();
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
                    var currentRow = $(this).parents('tr');
                    for (var i = 0; i < duration; i += 15) {
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
            Hyphenator.run();

        },
        doActivateTodo:function (e) {
            $(e.currentTarget).toggleClass('.day-plan-task-active');
        }
    });
})();
