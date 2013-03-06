<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <a class="brand" href="#">Admin panel</a>
        <ul class="nav">
            <li><a href="#universal-log">Universal</a></li>
            <li><a href="#activity-log">Activity</a></li>
            <li><a href="#mail-log">Mail</a></li>
            <li><a href="#dialog-log">Dialogs</a></li>
            <li><a href="#assessment-rules">Simulation Assessment Rules</a></li>
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
        <th>Point Code</th>
        <th>Point ID</th>
        <th>Value</th>
        <th>Type Scale</th>
    </tr>
    </thead>
    {foreach $simulation->assessment_points as $assessmentPoint}
        <tr>
            <td>{$assessmentPoint->point->code}</td>
            <td>{$assessmentPoint->point->title}</td>
            <td>{$assessmentPoint->value}</td>
            <td>{$assessmentPoint->point->type_scale}</td>
        </tr>
    {/foreach}
</table>

<h1 id="assessment-result">Assessment details</h1>

<table class="table table-striped assessment-details">
    <thead>
    <tr>
        <th>Point Code</th>
        <th>Point Description</th>
        <th>Type Scale</th>
        <th>Scale</th>
        <th>Replica ID</th>
        <th>Dialog Code</th>
        <th>Replica Step</th>
        <th>Replica Number</th>
        <th>Outbox mail</th>
    </tr>
    </thead>
    {foreach $simulation->assessment_dialog_points as $dialogPoint}
        <tr>
            <td>{$dialogPoint->point->code}</td>
            <td>{$dialogPoint->point->title}</td>
            <td>{$dialogPoint->point->type_scale}</td>
            <td>{$dialogPoint->point->scale}</td>
            <td>{$dialogPoint->dialog_id}</td>
            <td>{$dialogPoint->replica->code}</td>
            <td>{$dialogPoint->replica->step_number}</td>
            <td>{$dialogPoint->replica->replica_number}</td>
            <td>-</td>
        </tr>
    {/foreach}
    {foreach $simulation->simulation_mail_points as $mailPoints}
        <tr>
            <td>{$mailPoints->point->code}</td>
            <td>{$mailPoints->point->title}</td>
            <td>{$mailPoints->point->type_scale}</td>
            <td>{$mailPoints->point->scale}</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>
            {if $mailPoints->point->learning_goal_code eq 331 or $mailPoints->point->learning_goal_code eq 332}
                3. Оценка Mail Inbox
            {elseif $mailPoints->point->learning_goal_code eq 333}
                3. Оценка Mail Outbox
            {/if}
            </td>
        </tr>
    {/foreach}
    {foreach $simulation->getMailPointDetails() as $mailPoint}
        <tr>
            <td>{$dialogPoint->point->code}</td>
            <td>{$dialogPoint->point->title}</td>
            <td>{$dialogPoint->point->type_scale}</td>
            <td>{$dialogPoint->point->scale}</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>-</td>
            <td>{$mailPoint['out_mail_code']}</td>
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

<h1 id="assessment-rules">Simulation Assessment Rules</h1>

<table class="table table-striped mail-log">
    <thead>
    <tr>
        <th>Activity ID</th>
        <th>Scores</th>
    </tr>
    </thead>
    {foreach $simulation->getAssessmentRules() as $id => $rule}
        <tr>
            <td>{$rule->assessmentRule->activity_id}</td>
            <td>{$rule->assessmentRule->value}</td>
        </tr>
    {/foreach}
</table>
