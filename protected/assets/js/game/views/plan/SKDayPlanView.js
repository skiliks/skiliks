var SKDayPlanView;
/*global Backbone, _, SKApp, SKConfig, SKWindowView, Hyphenator, SKSingleWindowView*/
define(["game/views/SKWindowView"], function () {
    "use strict";

    /**
     * @type {SKDayPlanView}
     */
    SKDayPlanView = SKWindowView.extend(
        /** @lends SKDayPlanView.prototype */
        {
        'addClass': 'planner-book-main-div',
        'events':_.defaults(
            {
                'click .day-plan-todo-task':                                         'doActivateTodo',
                'dblclick .plan-todo .day-plan-todo-task':                           'doSetTask',
                'dblclick .planner-book-timetable-table .day-plan-todo-task.regular':'doUnSetTask',
                'click .todo-min':                                                   'doMinimizeTodo',
                'click .todo-max':                                                   'doMaximizeTodo',
                'click .todo-revert':                                                'doRestoreTodo'
            },
            SKWindowView.prototype.events
        ),

        setupDraggable:function () {
            var me = this,
            elements = this.$('.planner-task:not(.locked)');

            elements.draggable("destroy");
            var d31 = new Date();
            elements.draggable({
                addClasses: 'dragget-task',
                appendTo:".planner-book",
                containment:this.$('.planner-book'),
                cursorAt:{ top:4 },
                delay: 0,
                helper:'clone',
                revert: "invalid",
                scope: "tasks",
                scroll:true,
                snap:'td.planner-book-timetable-event-fl',
                snapMode:'inner',
                snapTolerance:11,
                stack:".planner-book",
                start:function () {
                    me.showDayPlanSlot($(this));
                    
                    var task_id = $(this).attr('data-task-id');
                    
                    var prev_cell = $(this).parents('td');
                    if (prev_cell.length) {
                        SKApp.user.simulation.dayplan_tasks.get(task_id).set('moving', true);
                    }
                    $(this).hide();
                    $(this).data("startingScrollTop", $(this).parent().scrollTop());
                    $(this).data("startingScrollLeft", $(this).parent().scrollLeft());
                },
                stop:function () {
                        me.hideDayPlanSlot($(this));
                        var task_id = $(this).attr('data-task-id');
                        var prev_cell = $(this).parents('td');
                        if (prev_cell.length) {
                            SKApp.user.simulation.dayplan_tasks.get(task_id).set('moving', false);
                        }
                        $(this).show();

                        // clean up highlighting, it is duplicate but it nessesary to place it here too
                        me.$('.drop-hover').removeClass('drop-hover');
                },
                drag:function (event, ui) {
                    var st = parseInt($(this).data("startingScrollTop"), 10);
                    var stl = parseInt($(this).data("startingScrollLeft"), 10);
                    ui.position.top -= $(this).parent().scrollTop() - st;
                    
                    // set height aacording duration {
                    var duration = parseInt($(this).attr('data-task-duration'), 10);
                    var height = me.calculateTaskHeigth(duration);
                    $('.planner-book .ui-draggable-dragging').height(height);
                    
                    // crop text length    
                    me.overflowText(
                        me.$('.ui-draggable-dragging .title'),
                        height, 
                        me.$('.ui-draggable-dragging .title')
                    );
                    // set height according duration }

                    ui.position.left -= $(this).parent().scrollLeft() - stl;
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
        hideDayPlanSlot:function (task_el) {
            var duration = parseInt(task_el.attr('data-task-duration'), 10);
            var prev_cell = task_el.parents('td');
            prev_cell.height(Math.ceil(duration / 15) * 11);
            prev_cell.find('.day-plan-td-slot')
                .hide();
            prev_cell
                .attr('rowspan', duration/15);
            prev_cell.find('.day-plan-todo-task').height(Math.ceil(duration / 15) * 11);
            var prevRow = task_el.parents('tr');
            for (var j = 0; j < duration - 15; j += 15) {
                prevRow = prevRow.next();
                prevRow
                    .find('.planner-book-timetable-event-fl, .planner-book-timetable-afterv-fl')
                    .hide();
            }
            return task_el;
        },
        showDayPlanSlot:function (task_el) {
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
            return task_el;
        }, removeDayPlanTask:function (task) {
            var task_el = this.$('div[data-task-id=' + task.id + ']');
            this.showDayPlanSlot(task_el);
            task_el.remove();
        },
        addDayPlanTask:function (model) {
            var me = this;
            var duration = parseInt(model.get('duration'), 10);

            var hour = model.get('date').split(':')[0];
            var minute = model.get('date').split(':')[1];
            var drop_td = this.$('div[data-day-id=' + model.get('day') + '] td[data-hour=' + hour + '][data-minute=' + minute + ']');
            drop_td.attr('rowspan', duration / 15);
            drop_td.find('.day-plan-td-slot').hide();
            drop_td.append(_.template($('#todo_task_template').html(), {task:model, type:'regular'}));
            if (model.get("type") === "2") {
                drop_td.find('.planner-task').addClass('locked');
            }
            
            // add title attribute to HTMl with full code
            drop_td.attr('title', drop_td.find('.title').text()); 
            
            var max_height = Math.ceil(duration / 15) * 10;
            setTimeout(function () {
                me.overflowText(drop_td.find('.title'), max_height, drop_td.find('.title'));
            }, 0);
            var todo_el = drop_td.find('.day-plan-todo-task');
            
            todo_el.height(me.calculateTaskHeigth(duration));
            drop_td.height(me.calculateTaskHeigth(duration));
            
            // Hiding next N cells
            var currentRow = drop_td.parents('tr');
            for (var i = 0; i < duration - 15; i += 15) {
                currentRow = currentRow.next();
                currentRow.find('.planner-book-timetable-event-fl, .planner-book-timetable-afterv-fl').hide();
            }
            
            // Updating draggable element list
            this.setupDraggable();
        },
        calculateTaskHeigth: function(duration) {
            return (Math.ceil(duration / 15) * 11)-2;
        },
        
        removeTodoTask:function (model) {
            this.$('.plan-todo div[data-task-id=' + model.id + ']').remove();
        },
        setupDroppable:function () {
            var me = this;
            me.shift = 0;
            
            var td_slot = this.$('.planner-book-today .day-plan-td-slot, .planner-book-tomorrow  .day-plan-td-slot');
            td_slot.droppable("destroy");
            td_slot.droppable({
                tolerance:"pointer",
                scope: "tasks",
                'drop':function (event, ui) {
                    // Reverting old element location
                    var task_id = ui.draggable.attr('data-task-id');
                    var prev_cell = ui.draggable.parents('td');
                    
                    var oldTask = {};
                    oldTask = ui.draggable.find('.title').text() + '';
                    
                    if (prev_cell.length) {
                        oldTask = SKApp.user.simulation.dayplan_tasks.get(task_id).get('title') + '';
                         
                        SKApp.user.simulation.dayplan_tasks.get(task_id).destroy();
                    }

                    if (ui.draggable.parents('.plan-todo').length) {
                        oldTask = SKApp.user.simulation.todo_tasks.get(task_id).get('title') + '';

                        SKApp.user.simulation.todo_tasks.get(task_id).destroy(); 
                    }

                    // Appending to new location
                    SKApp.user.simulation.dayplan_tasks.create({
                        title:    oldTask,
                        date:     $(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute'),
                        task_id:  task_id,
                        duration: ui.draggable.attr('data-task-duration'),
                        day:      $(this).parents('div[data-day-id]').attr('data-day-id')
                    });

                    // clean up highlighting, it is duplicate but it nessesary to place it here too
                    $('#plannerBook .drop-hover').removeClass('drop-hover');
                },
                over:function (event, ui) {
                    me.$('td.planner-book-timetable-event-fl').removeClass('drop-hover');
                    
                    // go last tr under dragged task {
                    var currentRow = $(this).parents('tr');
                    var duration = parseInt(ui.draggable.attr('data-task-duration'), 10);
                    for (var i = 0; i < duration; i += 15) {
                        currentRow = currentRow.next();
                    }
                    // go last tr under dragged task }
                    
                    // count time pieces
                    var rowsCount = currentRow.parent().parent().find('tr').length;
                    
                    // autoscroll to bottom
                    /* if (currentRow.index() < rowsCount && 0.75*rowsCount < currentRow.index()) {
                        currentRow.parent().parent().parent().parent().parent().mCustomScrollbar('scrollTo', 'last');
                    }else
                    // autoscroll to top    
                        if (1 < currentRow.index() && currentRow.index() < 0.5*rowsCount) {
                        currentRow.parent().parent().parent().parent().parent().mCustomScrollbar('scrollTo', 'first');
                    }  */
                    
                    // highlight time pieces {
                    $('.planner-book-timetable-table tr, .planner-book-after-vacation tr')
                        .removeClass('drop-hover');
                        
                    $(this).parent().parent().parent().parent()
                        .find('tr').addClass('drop-hover');  
                    // highlight time pieces }
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

                    return SKApp.user.simulation.dayplan_tasks.isTimeSlotFree(time, day, duration);
                }
            });
            var after_vacation_slot = this.$('.planner-book-afterv-table');
            after_vacation_slot.droppable("destroy");
            after_vacation_slot.droppable({
                hoverClass:"drop-hover",
                scope: "tasks",
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
                scope: "tasks",
                tolerance:"pointer",
                accept: function (draggable) {
                    return !draggable.parents('.plan-todo').length;
                },
                'drop':function (event, ui) {
                    // clean up highlighting, it is duplicate but it nessesary to place it here too
                    me.$('#plannerBook .drop-hover').removeClass('drop-hover');
                    var task_id = ui.draggable.attr('data-task-id');
                    
                    var oldTask = {};
                    oldTask = SKApp.user.simulation.dayplan_tasks.get(task_id).get('title');
                    
                    // Reverting old element location
                    SKApp.user.simulation.dayplan_tasks.get(task_id).destroy();

                    //Appending to new location
                    SKApp.user.simulation.todo_tasks.create({
                        title:    oldTask,
                        date:     $(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute'),
                        id:       task_id,
                        duration: ui.draggable.attr('data-task-duration'),
                        day:      $(this).parents('div[data-day-id]').attr('data-day-id')
                    });
                }
            });
        },
        updateTodos:function () {
            var me = this;
            this.$('.dayPlanTodoNum').html('(' + SKApp.user.simulation.todo_tasks.length + ')');
            me.$('.plan-todo-wrap .plan-todo-inner').html('');
            SKApp.user.simulation.todo_tasks.each(function (model) {
                var todo_task = $(_.template($('#todo_task_template').html(), {task:model, type:'todo'}));
                me.$('.plan-todo-wrap .plan-todo-inner').append(todo_task);
            });
            this.setupDraggable();

        },

        /**
         * Marks old slots and displays ruler
         */
        disableOldSlots:function () {
            if ('undefined' !== typeof SKApp.user.simulation) {
                this.$('.planner-book-today .planner-book-timetable-event-fl').each(function () {
                    var time = SKApp.user.simulation.getGameTime();
                    var cell_hour = parseInt($(this).attr('data-hour'), 10);
                    var current_hour = parseInt(time.split(':')[0], 10);
                    var cell_minute = parseInt($(this).attr('data-minute'), 10);
                    var current_minute = parseInt(time.split(':')[1], 10);
                    if (cell_hour < current_hour || (cell_hour === current_hour && cell_minute < current_minute)) {
                        $(this).addClass('past-slot');
                    }
                    $(this).find('.past').remove();
                    if (cell_hour === current_hour && cell_minute === parseInt(Math.floor(current_minute/15),10)*15) {
                        if (cell_minute === current_minute) {
                            $(this).prepend('<hr class="past" />');
                        } else {
                            $(this).append('<hr class="past" />');
                        }
                    }
                });
            }
        },

        /**
         * Renders title
         * @param title_el
         */
        renderTitle: function (title_el) {
            var me = this;
            title_el.html(_.template($('#plan_title_template').html(), {}));

        },

        /**
         * Renders inner part of the window
         * @param window_el
         */
        renderContent:function (window_el) {
            var me = this;
            window_el.html(_.template($('#plan_content_template').html(), {}));
            this.updateTodos();
            me.listenTo(SKApp.user.simulation.todo_tasks, 'add remove reset', function () {
                me.updateTodos();
            });
            me.listenTo(SKApp.user.simulation.todo_tasks, 'remove', function (model) {
                me.removeTodoTask(model);
            });
            SKApp.user.simulation.dayplan_tasks.each(function (model) {
                me.addDayPlanTask(model);
            });
            me.listenTo(SKApp.user.simulation.dayplan_tasks, 'remove', function (model) {
                me.removeDayPlanTask(model);
            });
            me.listenTo(SKApp.user.simulation.dayplan_tasks, 'add', function (model) {
                me.addDayPlanTask(model);
            });
            me.listenTo(SKApp.user.simulation, 'tick', function () {
                me.disableOldSlots();
            });
            me.$('.planner-book-timetable,.planner-book-afterv-table').mCustomScrollbar({autoDraggerLength:false});
            me.$('.plan-todo-wrap').mCustomScrollbar({autoDraggerLength:false});
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
        doUnSetTask:function (e) {
            var task_id = $(e.currentTarget).attr('data-task-id');
            var task = SKApp.user.simulation.dayplan_tasks.get(task_id);
            if(parseInt(task.get("type"),10) !== 2) {
                SKApp.user.simulation.dayplan_tasks.get(task_id).destroy();
                SKApp.user.simulation.todo_tasks.create({
                    title:task.get("title"),
                    date:task.get("date"),
                    id:task.get("task_id"),
                    duration:task.get("duration"),
                    day:task.get("day")
                });
            }

        },
        doMinimizeTodo:function () {
            this.$('.plan-todo').removeClass('open').removeClass('middle').addClass('closed');
            this.$('.planner-book-afterv-table').removeClass('closed').removeClass('half').addClass('full');
            this.$('.planner-book-timetable,.planner-book-afterv-table').mCustomScrollbar("update");
        },
        doMaximizeTodo:function () {
            this.$('.plan-todo').removeClass('closed').removeClass('middle').addClass('open');
            this.$('.planner-book-afterv-table').removeClass('full').removeClass('half').addClass('closed');
            this.$('.planner-book-timetable,.planner-book-afterv-table').mCustomScrollbar("update");

        },
        doRestoreTodo:function () {
            this.$('.plan-todo').removeClass('closed').removeClass('open').addClass('middle');
            this.$('.planner-book-afterv-table').removeClass('closed').removeClass('full').addClass('half');
            this.$('.planner-book-timetable, .planner-book-afterv-table').mCustomScrollbar("update");
        }
    });
});
