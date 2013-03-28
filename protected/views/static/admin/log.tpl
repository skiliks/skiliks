

<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <ul class="nav">
            {foreach $log_tables as $log_table}
                <li><a href="#{$log_table->getId()}">{$log_table->getTitle()}</a></li>
            {/foreach}
            <li><a href="#productivity">Productivity</a></li>
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

<a href="/simulation/developer/1">Start new simulation (dev,full)</a>

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
            <tr class="{$log_table->getRowId($row)}">
                {foreach $row as $cell}
                    <td>
                        {if (100 < strlen($cell))}
                            <span title="{str_replace('"',"'", $cell)}">{mb_substr($cell, 0, 50)}...</span>
                        {else}
                            {$cell}
                        {/if}
                    </td>
                {/foreach}
            </tr>
        {/foreach}
    </table>
{/foreach}

<a class="btn btn-primary selenium-tests-additional-tables-switcher"><i class="icon icon-list icon-white"></i> Дополнительные таблицы для Selenium тестов (показать/скрыть)</a>

<br>
<br>

<div class="selenium-tests-additional-tables" style="display: none;">
    <h1 id="simulation-points">Simulation points</h1>

    <table class="table table-striped mail-log">
        <thead>
        <tr>
            <th>Шкала</th>
            <th>Оценка</th>
        </tr>
        </thead>
        {foreach $simulation->getAssessmentSumByScale() as $typeScale => $assessmentPoint}
            <tr class="points-sum-scale-type-{HeroBehaviour::getTypeScaleName($typeScale)}">
                <td>{HeroBehaviour::getTypeScaleName($typeScale)}</td>
                <td>{$assessmentPoint}</td>
            </tr>
        {/foreach}
    </table>

    <h1 id="simulation-matrix-points">Assesments from dialod & mail matrix</h1>

    <table class="table table-striped mail-log">
        <thead>
        <tr>
            <th>Шкала</th>
            <th>Оценка</th>
        </tr>
        </thead>
        {foreach $simulation->getAssessmentPointsByScale() as $typeScale => $assessmentPoint}
            <tr class="matrix-points-sum-scale-type-{HeroBehaviour::getTypeScaleName($typeScale)}">
                <td>{HeroBehaviour::getTypeScaleName($typeScale)}</td>
                <td>{$assessmentPoint}</td>
            </tr>
        {/foreach}
    </table>

    <h1 id="productivity">Productivity</h1>

    <table class="table table-striped mail-log">
        <thead>
        <tr>
            <th>Activity ID</th>
            <th>Scores</th>
        </tr>
        </thead>
        {$sum = 0}
        {foreach $simulation->performance_points as $id => $point}
            <tr>
                <td>{$point->performanceRule->activity_id}</td>
                <td>{$point->performanceRule->value}</td>
            </tr>
            {$sum = $sum + $point->performanceRule->value}
        {/foreach}
        <tr class="assessment-rules-sum">
            <td>Итого</td>
            <td>{$sum}</td>
        </tr>
    </table>
</div>

<script type="text/javascript">
    $(".selenium-tests-additional-tables-switcher").click(function() {
        $(".selenium-tests-additional-tables").toggle();
    });
</script>

<br/>
<br/>
