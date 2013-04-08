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

    .nav li {
        display: block;
        height: auto;
    }

    #portamento_container {
        float:left;
        position:relative;
        width: 350px;
    }

    #portamento_container #flow-menu {
        float:none;
        position:absolute;
    }

    #portamento_container #flow-menu.fixed {
        position:fixed;
    }
</style>
<div class="container-fluid">
    <div class="row-fluid">

        <div class="span2">
            <div id="flow-menu-wrapper" class="row" style="overflow:">
                <div id="flow-menu" >
                    <ul class="nav nav-list nav-stacked span12">
                        {foreach $log_tables as $log_table}
                            <li><a href="#{$log_table->getId()}"><i class="icon-chevron-right pull-right"></i>{$log_table->getTitle()}</a></li>
                        {/foreach}
                    </ul>
                </div>
            </div>
        </div>

        <div class="table-list span10">
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
                <hr/>
            {/foreach}

            <hr/>

            <h3>Дополнительные таблицы для Selenium тестов:</h3>

            <hr/>

            <div class="selenium-tests-additional-tables">
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

                <hr/>

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
        $('.sidebar').affix();
        $('body').scrollspy();

        // flow menu
        $(document).ready(function(){
            $('#flow-menu').portamento();
        });
    </script>

</div>