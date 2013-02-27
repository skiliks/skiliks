<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <a class="brand" href="#">Admin panel</a>
        <ul class="nav">
            <li><a href="#universal-log">Universal</a></li>
            <li><a href="#activity-log">Activity</a></li>
            <li><a href="#mail-log">Mail</a></li>
            <li><a href="#dialog-log">Dialogs</a></li>
        </ul>
    </div>
</div>

<h1 id="simulation-info">Simulation</h1>
<dl>
    <dt>Id</dt>
    <dd>{$simulation.primaryKey}</dd>
</dl>

<h1 id="universal-log">Universal log</h1>

<table class="table table-striped universal-log">
    <thead>
    <tr>
        <th>Window Start Time</th>
        <th>Window End Time</th>
        <th>Window type</th>
        <th>Window subtype</th>
        <th>Window UID</th>
    </tr>
    </thead>
    {foreach $simulation->log_windows as $window}
        <tr>
            <td>{$window->start_time}</td>
            <td>{$window->end_time}</td>
            <td>{$window->window_obj->type}</td>
            <td>{$window->window_obj->subtype}</td>
            <td>{$window->window_uid}</td>
        </tr>
    {/foreach}
</table>

<h1 id="activity-log">Activity log</h1>

<table class="table table-striped leg-actions-log">
    <thead>
    <tr>
        <th>Window Start Time</th>
        <th>Window End Time</th>
        <th>Leg type</th>
        <th>Leg action</th>
        <th>Activity ID</th>
        <th>Category ID</th>
    </tr>
    </thead>
    {foreach $simulation->log_activity_actions as $log_activity_action}
        {assign var=action value=$log_activity_action->activityAction->getAction()}
        <tr {if $log_activity_action->activityAction->activity->category->code eq 1}class="success" {/if}>
            <td>{$log_activity_action->start_time}</td>
            <td>{$log_activity_action->end_time}</td>
            <td>{$log_activity_action->activityAction->leg_type}</td>
            <td>{if $action}{$action->getCode()}{/if}</td>
            <td>{$log_activity_action->activityAction->activity->primaryKey}</td>
            <td>{$log_activity_action->activityAction->activity->category->code}</td>

        </tr>
    {/foreach}
</table>

<h1 id="mail-log">Mail log</h1>

<table class="table table-striped mail-log">
    <thead>
    <tr>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Mail code</th>
        <th>Window</th>
    </tr>
    </thead>
    {foreach $simulation->log_mail as $log_mail}
        <tr>
            <td>{$log_mail->start_time}</td>
            <td>{$log_mail->end_time}</td>
            <td>{if $log_mail->mail}{$log_mail->mail->code}{/if}</td>
            <td>{$log_mail->window_obj->subtype}</td>
        </tr>
    {/foreach}
</table>

<h1 id="dialog-log">Dialog log</h1>

<table class="table table-striped mail-log">
    <thead>
    <tr>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Replica code</th>
        <th>Result replica</th>
    </tr>
    </thead>
    {foreach $simulation->log_dialogs as $log_dialog}
        <tr>
            <td>{$log_dialog->start_time}</td>
            <td>{$log_dialog->end_time}</td>
            <td>{$log_dialog->dialog->code}</td>
            <td>{$log_dialog->last_id}</td>
        </tr>
    {/foreach}
</table>

<h1 id="assessment-result">Assessment result</h1>

<table class="table table-striped mail-log">
    <thead>
    <tr>
        <th>Point ID</th>
        <th>Value</th>
        <th>Type Scale</th>
    </tr>
    </thead>
    {foreach $simulation->assessment_points as $assessmentPoint}
        <tr>
            <td>{$assessmentPoint->point->title}</td>
            <td>{$assessmentPoint->value}</td>
            <td>{$assessmentPoint->point->type_scale}</td>
        </tr>
    {/foreach}
</table>

<h1 id="simulation-points">Simulation points</h1>

<table class="table table-striped mail-log">
    <thead>
    <tr>
        <th>Шкала</th>
        <th>Оценка</th>
    </tr>
    </thead>
    {foreach $simulation->getAssessmentResults() as $typeScale => $assessmentPoint}
        <tr>
            <td>{$typeScale}</td>
            <td>{$assessmentPoint}</td>
        </tr>
    {/foreach}
</table>

<h1 id="simulation-assessment-rules">Simulation Assessment Rules</h1>

<table class="table table-striped mail-log">
    <thead>
    <tr>
        <th>Activity ID</th>
        <th>Scores</th>
    </tr>
    </thead>
    {foreach $simulation->getAssessmentRules() as $typeScale => $assessmentPoint}
        <tr>
            <td>test</td>
            <td>test</td>
        </tr>
    {/foreach}
</table>
