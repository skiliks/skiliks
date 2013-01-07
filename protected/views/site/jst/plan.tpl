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
<script type="text/template" id="plan_template">
<div class="planner-book-main-div">
<div id="plannerBookQuarterPlan" class="planner-book-quarter-plan">
    <img src="<@= SKConfig.assetsUrl @>/img/planner/plan_quarter.png">
</div>
<div id="plannerBookDayPlan" class="planner-book-day-plan">
    <img src="<@= SKConfig.assetsUrl @>/img/planner/plan_day.png">
</div>
<div class="btn-close" onclick="dayPlan.draw();">
    <button></button>
</div>
<div id="plannerBook" class="planner-book">
<div id="plannerBookToday" class="planner-book-today">
    <div class="planner-book-today-head">
        <img src="<@= SKConfig.assetsUrl @>/img/planner/type-today.png">
    </div>
    <div id="plannerBookTodayTimeTable" class="planner-book-timetable">
        <table class="planner-book-timetable-table">
            <@ _.range(9,22).forEach(function(hour) { @>
            <@ ['00', '15', '30', '45'].forEach(function(minute){ @>
            <tr>
                <@ if (minute === '00') { @>
                <td class="planner-book-timetable-time-fl" rowspan="2"><@= hour @>:<@= minute @></td>
                <@ } @>
                <@ if (minute === '30') { @>
                <td class="planner-book-timetable-time-fl" rowspan="2"></td>
                <@ } @>
                <td data-hour="<@= hour @>" data-minute="<@= minute @>"
                    class="planner-book-timetable-event-fl bdb day-plan-td-slot ui-droppable"></td>
            </tr>
            <@ }) @>
            <@ }) @>
        </table>
    </div>
</div>
<div id="plannerBookTomorrow" class="planner-book-tomorrow">
    <div class="planner-book-tomorrow-head">
        <img src="<@= SKConfig.assetsUrl @>/img/planner/type-tomorrow.png">
    </div>
    <div id="plannerBookTomorrowTimeTable" class="planner-book-timetable">
        <table class="planner-book-timetable-table">
            <@ _.range(9,22).forEach(function(hour) { @>
            <@ ['00', '15', '30', '45'].forEach(function(minute){ @>
            <tr>
                <@ if (minute === '00') { @>
                <td class="planner-book-timetable-time-fl" rowspan="2"><@= hour @>:<@= minute @></td>
                <@ } @>
                <@ if (minute === '30') { @>
                <td class="planner-book-timetable-time-fl" rowspan="2"></td>
                <@ } @>
                <td data-hour="<@= hour @>" data-minute="<@= minute @>"
                    class="planner-book-timetable-event-fl bdb day-plan-td-slot ui-droppable"></td>
            </tr>
            <@ }) @>
            <@ }) @>
        </table>
    </div>
    <div class="planner-book-scrollbar"></div>
</div>
<div id="plannerBookAfterVacation" class="planner-book-after-vacation">
    <div class="planner-book-after-vacation-head">
        <img src="<@= SKConfig.assetsUrl @>/img/planner/type-after-vacation.png">
    </div>
    <div id="plannerBookAfterVacationTable" class="planner-book-afterv-table">
        <table class="planner-book-timetable-table">
            <@ _.range(9,22).forEach(function(hour) { @>
            <@ ['00', '15', '30', '45'].forEach(function(minute){ @>
            <tr>
                <td data-hour="<@= hour @>" data-minute="<@= minute @>"
                    class="planner-book-timetable-event-fl bdb day-plan-td-slot ui-droppable"></td>
            </tr>
            <@ }) @>
            <@ }) @>
        </table>
    </div>
    <div id="plannerBookAfterVacationScrollbar" class="planner-book-afterv-scrollbar"></div>
</div>
</div>
</div>
</script>