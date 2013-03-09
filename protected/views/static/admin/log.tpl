<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <a class="brand" href="#">Admin panel</a>
        <ul class="nav">
            {foreach $log_tables as $log_table}
            <li><a href="#{$log_table->getId()}">{$log_table->getTitle()}</a></li>
            {/foreach}
            <li><a href="#assessment-rules">Simulation Assessment Rules</a></li>
        </ul>
    </div>
</div>

<h1 id="simulation-info">Simulation</h1>
<dl>
    <dt>Id</dt>
    <dd>{$simulation.primaryKey}</dd>
</dl>

<a href="/admin/saveLog?simulation=2">Save log as XLS</a>

{foreach $log_tables as $log_table}
    <h1 id="{$log_table->getId()}">{$log_table->getTitle()}</h1>

    <table class="table table-striped {$log_table->getId()}">
        <thead>
        <tr>
            {foreach $log_table->getHeaders() as $header}
                <th>{$header}</th>
            {/foreach}
        </tr>
        </thead>
        {foreach $log_table->getData() as $row}
            <tr>
                {foreach $row as $cell}
                    <td>{$cell}</td>
                {/foreach}
            </tr>
        {/foreach}
    </table>
{/foreach}

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
