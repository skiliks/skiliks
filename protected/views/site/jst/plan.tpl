<script type="text/template" id="todo_template">
    <div class="plan-todo middle" id="plan-todoM">
        <ul class="plan-todo-btn">
            <li>
                <button class="max" onclick="dayPlan.todoViewChange('up');"></button>
            </li>
            <li>
                <button class="min" onclick="dayPlan.todoViewChange('down');"></button>
            </li>
        </ul>

        <p class="plan-todo-tit">Сделать <span id="dayPlanTodoNum">(19)</span></p>

        <div class="plan-todo-wrap" id="dayPlanToDoDivScroll" style="float:left;"></div>
        <div id="plannerDayPlanScrollbar" class="planner-dayplan-scrollbar" style="float:left;margin-top:50px;"></div>
    </div>
</script>
<script type="text/template" id="plan_title_template">
    <div>
        <div class="planner-book-head planner-book-today-head">
            <div>
            	<img src="<@= SKConfig.assetsUrl @>/img/planner/type-today.png">
            	<table>
            		<tr>
            			<td>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            			</td>
            			<td>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            			</td>
            			<td>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            			</td>
            		</tr>
            	</table>
            </div>
        </div>
        <div class="planner-book-head planner-book-tomorrow-head">
            <div>
            	<img src="<@= SKConfig.assetsUrl @>/img/planner/type-tomorrow.png">
            	<table>
            		<tr>
            			<td>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            			</td>
            			<td>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            			</td>
            			<td>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            			</td>
            		</tr>
            	</table>
            </div>
        </div>
        <div class="planner-book-head planner-book-after-vacation-head">
            <div>
            	<img src="<@= SKConfig.assetsUrl @>/img/planner/type-after-vacation.png">
            	<table>
            		<tr>
            			<td>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            			</td>
            			<td>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            			</td>
            			<td>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-l"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            				<span class="stroke-r"></span>
            			</td>
            		</tr>
            	</table>
            </div>
        </div>
    </div>
</script>
<script type="text/template" id="plan_content_template">
    <div>
        <div id="plannerBookQuarterPlan" class="planner-book-quarter-plan">
            <img src="<@= SKConfig.assetsUrl @>/img/planner/plan_quarter.png">
        </div>
        <div id="plannerBookDayPlan" class="planner-book-day-plan">
            <img src="<@= SKConfig.assetsUrl @>/img/planner/plan_day.png">
        </div>
        <div class="btn-close win-close">
            <button></button>
        </div>
        <div id="plannerBook" class="planner-book">
            <div id="plannerBookToday" class="planner-book-today" data-day-id="1">
                <div id="plannerBookTodayTimeTable" class="planner-book-timetable">
                    <table class="planner-book-timetable-table">
                        <@ _.range(9,22).forEach(function(hour) { @>

                        <@ ['00', '15', '30', '45'].forEach(function(minute){ @>
                        <tr>
                            <@ if (minute === '00') { @>
                            <td class="planner-book-timetable-time-fl" rowspan="4"><@= hour @>:<@= minute @></td>
                            <@ } @>
                            <td data-hour="<@= hour @>" data-minute="<@= minute @>"
                                class="planner-book-timetable-event-fl bdb"><div class="day-plan-td-slot"></div></td>
                        </tr>
                        <@ }) @>
                        <@ }) @>
                    </table>
                </div>
            </div>
            <div id="plannerBookTomorrow" class="planner-book-tomorrow"  data-day-id="2">
                <div id="plannerBookTomorrowTimeTable" class="planner-book-timetable">
                    <table class="planner-book-timetable-table">
                        <@ _.range(9,22).forEach(function(hour) { @>
                        <@ ['00', '15', '30', '45'].forEach(function(minute){ @>
                        <tr>
                            <@ if (minute === '00') { @>
                            <td class="planner-book-timetable-time-fl" rowspan="4"><@= hour @>:<@= minute @></td>
                            <@ } @>
                            <td data-hour="<@= hour @>" data-minute="<@= minute @>"
                                class="planner-book-timetable-event-fl bdb ui-droppable"><div class="day-plan-td-slot"></div></td>
                        </tr>
                        <@ }) @>
                        <@ }) @>
                    </table>
                </div>
            </div>
            <div id="plannerBookAfterVacation" class="planner-book-after-vacation"  data-day-id="3">
                <div id="plannerBookAfterVacationTable" class="planner-book-afterv-table half">
                    <table class="planner-book-timetable-table">
                        <@ _.range(9,22).forEach(function(hour) { @>
                        <@ ['00', '15', '30', '45'].forEach(function(minute){ @>
                        <tr>
                            <td data-hour="<@= hour @>" data-minute="<@= minute @>"
                                class="planner-book-timetable-afterv-fl ui-droppable"><div class="day-plan-td-slot"></div></td>
                        </tr>
                        <@ }) @>
                        <@ }) @>
                    </table>
                </div>
            </div>
            <div class="plan-todo middle">
				<div>
					<ul class="plan-todo-btn">
						<li>
							<button class="todo-min min"></button>
							<button class="todo-revert min"></button>
							<button class="todo-revert max"></button>
							<button class="todo-max max"></button>
						</li>
					</ul>
                    	
					<p class="plan-todo-tit">Сделать <span class="dayPlanTodoNum"></span></p>
					<div class="plan-todo-wrap"><div class="plan-todo-inner"></div></div>
				</div>
			</div>
        </div>
    </div>
</script>
<script type="text/template" id="todo_task_template">
    <div class="planner-task day-plan-todo-task <@ if (type === 'regular') { @>regular<@ } @>"
         data-task-id="<@= task.id @>"
         data-task-duration="<@= task.get('duration') @>">
    <span class="title hyphenate"><@= task.get('title') @></span>
        <div class="duration"><p><span><@= task.get('duration') @></span><br />мин</p></div>
</script>