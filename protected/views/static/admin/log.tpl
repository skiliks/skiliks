

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
            <tr class="{$log_table->getRowId($row)}">
                {foreach $row as $cell}
                    <td>
                        {if (40 < strlen($cell))}
                            <span title="{str_replace('"',"'", $cell)}">{mb_substr($cell, 0, 20)}...</span>
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

    <h1 id="assessment-rules">Simulation Assessment Rules</h1>

    <table class="table table-striped mail-log">
        <thead>
        <tr>
            <th>Activity ID</th>
            <th>Scores</th>
        </tr>
        </thead>
        {$sum = 0}
        {foreach $simulation->getAssessmentRules() as $id => $rule}
            <tr>
                <td>{$rule->assessmentRule->activity_id}</td>
                <td>{$rule->assessmentRule->value}</td>
            </tr>
            {$sum = $sum + $rule->assessmentRule->value}
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
