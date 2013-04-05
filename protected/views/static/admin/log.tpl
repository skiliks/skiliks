<style>
    .sidebar {
        width: 420px;
    }
    .affix, .affix-bottom {
        position: fixed;
    }
    .sidebar li {
        height: auto;
    }
    .sidebar .active a {
        background-color: #f6f6f6;
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">

        <div class="span3">
            <ul class="nav nav-tabs nav-stacked sidebar">
                {foreach $log_tables as $log_table}
                    <li><a href="#{$log_table->getId()}"><i class="icon-chevron-right pull-right"></i>{$log_table->getTitle()}</a></li>
                {/foreach}
            </ul>
        </div>

        <div class="table-list span9">
            <h1 id="simulation-info">Simulation: # {$simulation.primaryKey}</h1>

            <div class="well">
                <a class="btn" href="/static/admin/saveLog/{$simulation->primaryKey}">Save log as XLS</a>
                <a class="btn" href="/simulation/developer/full">Start new simulation (dev,full)</a>
            </div>

            {foreach $log_tables as $log_table}
                <h1 id="{$log_table->getId()}">
                    {$log_table->getTitle()}
                </h1>
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
            </div>

        </div>
    </div>

    <script type="text/javascript">
        $(".selenium-tests-additional-tables-switcher").click(function() {
            $(".selenium-tests-additional-tables").toggle();
        });

        $('.sidebar').affix();
        $('body').scrollspy();
    </script>

</div>