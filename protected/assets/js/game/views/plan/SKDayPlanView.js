
var SKDayPlanView;

/*global Backbone, _, SKApp, SKConfig, SKWindowView, Hyphenator, SKSingleWindowView, $, define, SKDialogView*/
define([
    "text!game/jst/plan/todo_task_template.jst",
    "text!game/jst/plan/plan_title_template.jst",
    "text!game/jst/plan/plan_content_template.jst",
    "text!game/jst/plan/plan_hint_template.jst",

    "game/views/SKWindowView"
], function (
    todo_task_template,
    plan_title_template,
    plan_content_template,
    plan_hint_template
) {
    "use strict";

    /**
     * @class SKDayPlanView
     * @augments Backbone.View
     */
    SKDayPlanView = SKWindowView.extend({
        addClass: 'planner-book-main-div plan-window',
        addId: 'plan-window',
        isDisplaySettingsButton:false,

        dimensions: {
            maxWidth: 1100,
            maxHeight: 700
        },

        'events':_.defaults(
            {
                'click .day-plan-todo-task':                                         'onActivateTodo',
                'dblclick .plan-todo .day-plan-todo-task':                           'doSetTask',
                'dblclick .planner-book-timetable-table .day-plan-todo-task.regular':'doUnSetTask',
                'click .todo-min':                                                   'doMinimizeTodo',
                'click .todo-max':                                                   'doMaximizeTodo',
                'click .todo-revert':                                                'doRestoreTodo',
                'click #plannerBookQuarterPlan':                                     'doPlannerBookQuarterPlan',
                'click #plannerBookDayPlan':                                         'doPlannerBookDayPlan',
                'click .save-day-plan':                                              'doSaveTomorrowPlan',
                'webkitTransitionEnd .plan-todo':                                    'doTransitionEnd',
                'mouseout .planner-book-timetable-event-fl .day-plan-todo-task.day-plan-task-active':'hideHint',
                'mouseout .planner-book-timetable-afterv-fl .day-plan-todo-task.day-plan-task-active':'hideHint'

            },
            SKWindowView.prototype.events
        ),

        /**
         * @method
         */
        setupDraggable:function () {
            try {
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
                    snapTolerance:12,
                    stack:".planner-book",
                    start:function () {
                        me.showDayPlanSlot($(this));

                        var task_id = $(this).attr('data-task-id');

                        var prev_cell = $(this).parents('td');
                        if (prev_cell.length) {
                            SKApp.simulation.dayplan_tasks.get(task_id).set('moving', true);
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
                                SKApp.simulation.dayplan_tasks.get(task_id).set('moving', false);
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Stripping text until it fits in specific height
         *
         * @method
         * @param el
         * @param max_height
         * @param text_el
         */
        overflowText:function (el, max_height, text_el) {
            try {
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param task_el
         * @returns {*}
         */
        hideDayPlanSlot:function (task_el) {
            try {
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param task_el
         * @returns {*}
         */
        showDayPlanSlot:function (task_el) {
            try {
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param task
         */
        removeDayPlanTask:function (task) {
            try {
                var task_el = this.$('div[data-task-id=' + task.id + ']');
                this.showDayPlanSlot(task_el);
                task_el.remove();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param model
         */
        addDayPlanTask:function (model) {
            try {
                var me = this;
                var duration = parseInt(model.get('duration'), 10);

                var hour = model.get('date').split(':')[0];
                var minute = model.get('date').split(':')[1];
                var drop_td = this.$('div[data-day-id=' + model.get('day') + '] td[data-hour=' + hour + '][data-minute=' + minute + ']');
                drop_td.attr('rowspan', duration / 15);
                drop_td.find('.day-plan-td-slot').hide();
                drop_td.append(_.template(todo_task_template, {task:model, type:'regular'}));
                if (model.get("type") === "1") {
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param duration
         * @returns {number}
         */
        calculateTaskHeigth: function(duration) {
        	if (duration > 30){
        		return duration / 15 * 11;
        	}
            else{
            	return (duration / 15 * 11) - 2;
            }
        },

        /**
         * @method
         * @param model
         */
        removeTodoTask:function (model) {
            try {
                this.$('.plan-todo div[data-task-id=' + model.id + ']').remove();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        setupDroppable:function () {
            try {
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

    //                    var index = Math.round((ui.offset.top - $(this).find('table').offset().top) / 12),
    //                        tdCell = $(event.target).find('tr:eq(' + index + ') td.planner-book-timetable-event-fl'),
    //                        task_id = ui.draggable.attr('data-task-id'),
    //                        prev_cell = ui.draggable.parents('td'),
    //                        time = tdCell.attr('data-hour') + ':' + tdCell.attr('data-minute'),
    //                        day = $(this).attr('data-day-id'),
    //                        duration = ui.draggable.attr('data-task-duration');
    //
    //                    if (false === SKApp.simulation.dayplan_tasks.isTimeSlotFree(time, day, duration)) {
    //                        return false;
    //                    }


                        var oldTask = {};
                        oldTask = ui.draggable.find('.title').text() + '';

                        if (prev_cell.length) {
                            oldTask = SKApp.simulation.dayplan_tasks.get(task_id).get('title') + '';

                            SKApp.simulation.dayplan_tasks.get(task_id).destroy();
                        }

                        if (ui.draggable.parents('.plan-todo').length) {
                            oldTask = SKApp.simulation.todo_tasks.get(task_id).get('title') + '';

                            SKApp.simulation.todo_tasks.get(task_id).destroy();
                        }

                        // Appending to new location
                        SKApp.simulation.dayplan_tasks.create({
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

    //                    var index = Math.round((ui.offset.top - $(this).find('table').offset().top) / 12);
    //                    var currentRow = $(event.target).find('tr:eq(' + index + ')');

                        var duration = parseInt(ui.draggable.attr('data-task-duration'), 10);
                        for (var i = 0; i < duration; i += 15) {
                            currentRow = currentRow.next();
                        }
                        // go last tr under dragged task }

                        // count time pieces
                        var rowsCount = currentRow.parent().parent().find('tr').length;

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
                        var parent = $(this).parent();
                        var time = parent.attr('data-hour') + ':' + parent.attr('data-minute');

                        return SKApp.simulation.dayplan_tasks.isTimeSlotFree(time, day, duration);
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
                            var duration = ui.draggable.attr('data-task-duration'),
                                day = $(this).parents('div[data-day-id]').attr('data-day-id'),
                                time = $(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute');

                            if (SKApp.simulation.dayplan_tasks.isTimeSlotFree(time, day, duration)) {
                                // Reverting old element location
                                var task_id = ui.draggable.attr('data-task-id');
                                var prev_cell = ui.draggable.parents('td');

                                if (prev_cell.length) {
                                    SKApp.simulation.dayplan_tasks.get(task_id).destroy();
                                }

                                if (ui.draggable.parents('.plan-todo').length) {
                                    SKApp.simulation.todo_tasks.get(task_id).destroy();
                                }

                                //Appending to new location
                                SKApp.simulation.dayplan_tasks.create({
                                    title:ui.draggable.find('.title').text(),
                                    date:time,
                                    task_id:task_id,
                                    duration:duration,
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
                        oldTask = SKApp.simulation.dayplan_tasks.get(task_id).get('title');

                        // Reverting old element location
                        SKApp.simulation.dayplan_tasks.get(task_id).destroy();

                        //Appending to new location
                        SKApp.simulation.todo_tasks.create({
                            title:    oldTask,
                            date:     $(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute'),
                            id:       task_id,
                            duration: ui.draggable.attr('data-task-duration'),
                            day:      $(this).parents('div[data-day-id]').attr('data-day-id')
                        });
                    }
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        updateTodos:function () {
            try {
                var me = this;
                this.$('.dayPlanTodoNum').html('(' + SKApp.simulation.todo_tasks.length + ')');
                me.$('.plan-todo-wrap .plan-todo-inner').html('');
                SKApp.simulation.todo_tasks.each(function (model) {
                    var todo_task = $(_.template(todo_task_template, {task:model, type:'todo'}));
                    me.$('.plan-todo-wrap .plan-todo-inner').append(todo_task);
                });
                this.setupDraggable();
                this.$('.plan-todo-wrap').mCustomScrollbar("update");
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Marks old slots and displays ruler
         *
         * @method
         */
        disableOldSlots:function () {
            try {
                var me = this;

                if ('undefined' !== typeof SKApp.simulation) {
                    me.$('.planner-book-today .planner-book-timetable-event-fl').each(function () {
                        var time = SKApp.simulation.getGameTime();
                        var cell_hour = parseInt($(this).attr('data-hour'), 10);
                        var current_hour = parseInt(time.split(':')[0], 10);
                        var cell_minute = parseInt($(this).attr('data-minute'), 10);
                        var current_minute = parseInt(time.split(':')[1], 10);
                        if (cell_hour < current_hour || (cell_hour === current_hour && cell_minute < current_minute)) {
                            $(this).addClass('past-slot');
                        }

                        if (me.$('.past').length === 0) {
                            me.$('.planner-book-today .planner-book-timetable-table').after('<hr class="past"/>');
                        }

                        me.$('.past').css('top', ((current_hour - 9 + current_minute / 60) * 46) + 'px');
                    });
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * Renders title
         *
         * @method
         * @param title_el
         */
        renderTitle: function (title_el) {
            try {
                var me = this;
                title_el.html(_.template(plan_title_template, {}));
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }

        },

        /**
         * Renders inner part of the window
         *
         * @method
         * @param window_el
         */
        renderContent:function (window_el) {
            try {
                var me = this;
                window_el.html(_.template(plan_content_template, {isDisplaySettingsButton:this.isDisplaySettingsButton}));
                this.updateTodos();
                me.listenTo(SKApp.simulation.todo_tasks, 'add remove reset', function () {
                    me.updateTodos();
                });
                me.listenTo(SKApp.simulation.todo_tasks, 'remove', function (model) {
                    me.removeTodoTask(model);
                });
                SKApp.simulation.dayplan_tasks.each(function (model) {
                    me.addDayPlanTask(model);
                });
                me.listenTo(SKApp.simulation.dayplan_tasks, 'remove', function (model) {
                    me.removeDayPlanTask(model);
                });
                me.listenTo(SKApp.simulation.dayplan_tasks, 'add', function (model) {
                    me.addDayPlanTask(model);
                });
                me.listenTo(SKApp.simulation, 'tick', me.disableOldSlots);
                setTimeout(function () {
                    me.disableOldSlots();
                    me.$('.planner-book-timetable,.planner-book-afterv-table').mCustomScrollbar({autoDraggerLength:false, updateOnContentResize: true});
                    me.$('.plan-todo-wrap').mCustomScrollbar({autoDraggerLength:false, updateOnContentResize:true});
                }, 0);

                this.setupDroppable();
                Hyphenator.run();
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param e
         */
        onActivateTodo: function(e) {
            try {
                var taskId = this.$(e.currentTarget).attr('data-task-id');
                this.doActivateTodo(taskId, e);
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        doActivateTodo:function (taskId, e) {
            try {
                var $task = this.$('.day-plan-todo-task[data-task-id=' + taskId + ']'),
                    active = $task.hasClass('day-plan-task-active');

                this.$('.day-plan-task-active').removeClass('day-plan-task-active');
                $task.toggleClass('day-plan-task-active', !active);

                if(_.isEmpty($task.attr('data-task-day')) === false){
                    // SKILIKS-3628
                    // this.showHint($task);
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param e
         */
        doSetTask:function (e) {
            try {
                this.$('.plan-todo-wrap').mCustomScrollbar("update");
                var me = this;
                var task_id = $(e.currentTarget).attr('data-task-id');
                var task = SKApp.simulation.todo_tasks.get(task_id);
                me.$('.day-plan-td-slot').each(function () {
                    var duration = task.get('duration');
                    task.set('day', $(this).parents('div[data-day-id]').attr('data-day-id'));
                    task.set('date', $(this).parent().attr('data-hour') + ':' + $(this).parent().attr('data-minute'));
                    if (SKApp.simulation.dayplan_tasks.isTimeSlotFree(task.get('date'), task.get('day'), duration)) {
                        task.destroy();
                        SKApp.simulation.dayplan_tasks.create({
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
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param e
         */
        doUnSetTask:function (e) {
            try {
                this.$('.plan-todo-wrap').mCustomScrollbar("update");
                var task_id = $(e.currentTarget).attr('data-task-id');
                var task = SKApp.simulation.dayplan_tasks.get(task_id);
                if(parseInt(task.get("type"),10) !== 1) {
                    SKApp.simulation.dayplan_tasks.get(task_id).destroy();
                    SKApp.simulation.todo_tasks.create({
                        title:task.get("title"),
                        date:task.get("date"),
                        id:task.get("task_id"),
                        duration:task.get("duration"),
                        day:task.get("day")
                    });
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        doMinimizeTodo:function () {
            try {
                this.$('.plan-todo').removeClass('open middle').addClass('closed');
                this.$('.planner-book-afterv-table').removeClass('closed half').addClass('full');
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        doMaximizeTodo:function () {
            try {
                this.$('.plan-todo').removeClass('closed middle').addClass('open');
                this.$('.planner-book-afterv-table').removeClass('full half').addClass('closed');
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         */
        doRestoreTodo:function () {
            this.$('.plan-todo').removeClass('closed open').addClass('middle');
            this.$('.planner-book-afterv-table').removeClass('closed full').addClass('half');
        },

        /**
         * @method
         * @param e
         */
        doPlannerBookQuarterPlan:function(e) {
            try {
                if(!$(e.currentTarget).hasClass('is-active-plan-tab')){
                    var tab = $('.is-active-plan-tab');
                    tab.css('cursor', 'pointer');
                    tab.removeClass('is-active-plan-tab');
                    tab.children('img').attr('src', SKApp.get('assetsUrl')+'/img/planner/plan_day.png');
                    $(e.currentTarget).css('cursor','default');
                    $(e.currentTarget).addClass('is-active-plan-tab');
                    $(e.currentTarget).children('img').attr('src', SKApp.get('assetsUrl')+'/img/planner/plan_quarter-active.png');
                    $('.plannerBookDayPlan').css('display', 'none');
                    $('.plannerBookQuarterPlan').css('display', 'block');
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        /**
         * @method
         * @param e
         */
        doPlannerBookDayPlan:function(e) {
            try {
                if(!$(e.currentTarget).hasClass('is-active-plan-tab')){
                    var tab = $('.is-active-plan-tab');
                    tab.css('cursor', 'pointer');
                    tab.removeClass('is-active-plan-tab');
                    tab.children('img').attr('src', SKApp.get('assetsUrl')+'/img/planner/plan_quarter.png');
                    $(e.currentTarget).css('cursor','default');
                    $(e.currentTarget).addClass('is-active-plan-tab');
                    $(e.currentTarget).children('img').attr('src', SKApp.get('assetsUrl')+'/img/planner/plan_day-active.png');
                    $('.plannerBookDayPlan').css('display', 'block');
                    $('.plannerBookQuarterPlan').css('display', 'none');
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        doSaveTomorrowPlan: function() {
            try {
                SKApp.simulation.savePlan(function() {
                    var dialog = new SKDialogView({
                        message: 'Ваш план успешно сохранен',
                        buttons: [{
                            id: 'close',
                            value: 'Ok'
                        }]
                    });
                });
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },

        doTransitionEnd:function() {
            try {
                this.$('.planner-book-afterv-table').mCustomScrollbar("update");
                this.$('.planner-book-timetable').mCustomScrollbar("update");
                this.$('.plan-todo-wrap').mCustomScrollbar("update");
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        showHint:function($task) {
            try {
                var position = $task.parent().offset();
                var width = $task.parent().width();
                var height = $task.height();
                console.log(position);
                var title = $task.parent('td').attr('title');
                if(_.isEmpty(this.$('.plan_hint_tooltip')) === false){
                    $('.plan_hint_tooltip').remove();
                }
                if($task.hasClass('day-plan-task-active')) {
                    $('.canvas').find('.windows-container').append(_.template(plan_hint_template, {title:title}));
                    $('.plan_hint_tooltip').css('width', (width-6)+'px');
                    var height_tooltip = $('.plan_hint_tooltip').height();
                    $('.plan_hint_tooltip').css('top', (position.top - height_tooltip -50)+'px');
                    $('.plan_hint_tooltip').css('left', (position.left-10)+'px');
                }
            } catch(exception) {
                if (window.Raven) {
                    window.Raven.captureMessage(exception.message + ',' + exception.stack);
                }
            }
        },
        hideHint:function(e) {
            $('.plan_hint_tooltip').remove();
        }
    });

    return SKDayPlanView;
});
