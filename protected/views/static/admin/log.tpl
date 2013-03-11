<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <a class="brand" href="#">Admin panel</a>
        <ul class="nav">
            {foreach $log_tables as $log_table}
            <li><a href="#{$log_table->getId()}">{$log_table->getTitle()}</a></li>
            {/foreach}
            <li><a href="#assessment-rules">Simulation Assessment Rules</a></li>
            <li><a href="#excel">Excel</a></li>
        </ul>
    </div>
</div>

<h1 id="simulation-info">Simulation</h1>
<dl>
    <dt>Id</dt>
    <dd>{$simulation.primaryKey}</dd>
</dl>

<a href="/static/admin/saveLog?simulation={$simulation->primaryKey}">Save log as XLS</a>

&nbsp; &nbsp; &nbsp;

<a href="/simulation">Start new simulation</a>

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
                    <td>
                        {if (40 < strlen($cell))}
                            <span title="{str_replace('"',"'", $cell)}">{substr($cell, 0, 20)}...</span>
                        {else}
                            {$cell}
                        {/if}
                    </td>
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

<h1 id="excel">Логирование - Excel</h1>

<table class="table table-striped mail-log">
    <thead>
    <tr>
        <th>id_симуляции</th>
        <th>Номер формулы</th>
        <th>Оценка (0 или 1)</th>
    </tr>
    </thead>
    {foreach $simulation->simulation_excel_points as $point}
        <tr>
            <td>{$point->sim_id}</td>
            <td>{$point->formula_id}</td>
            <td>{$point->value}</td>
        </tr>
    {/foreach}
</table>

