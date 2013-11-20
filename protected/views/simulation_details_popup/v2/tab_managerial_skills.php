<div class="extrasidepads managerialmain">
    <div class="textcener"><h2 class="total totalwithresult">Управленческие навыки <span class="value blockvalue managerial-skills"></span></h2></div>
    <div class="clearfix">
        <div class="labeltitles">
            <h3>По группам навыков</h3>
        </div>
        <p class="barstitle mangrlresultstitle">Уровень владения навыком</p>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels high-labels">
            <div class="labelwrap"><span class="thelabel"><div><span class="list-counter">1.</span><a href="#managerial-skills-1-2" class="list-text">Управление задачами с учётом приоритетов</a><a href="#managerial-skills-1-2" class="signmore"></a></div></span></div>
            <div class="labelwrap"><span class="thelabel"><div><span class="list-counter">2.</span><a href="#managerial-skills-3-4" class="list-text">Управление людьми</a><a href="#managerial-skills-3-4" class="signmore"></a></div></span></div>
            <div class="labelwrap"><span class="thelabel"><div><span class="list-counter">3.</span><a href="#managerial-skills-3-4" class="list-text">Управление коммуникациями</a><a href="#managerial-skills-3-4" class="signmore"></a></div></span></div>
        </div>
        <div class="barswrap main-skills">
            <div class="chartbar management-1"></div>
            <div class="chartbar management-2"></div>
            <div class="chartbar management-3"></div>
        </div>
    </div>

    <div class="legendwrap resultslegend">
        <div class="legend">
            <p class="barstitle">Обозначения</p>
            <div class="legendvalue shortlegend"><span class="legendcolor colormax"></span><span class="legendtitle">Максимальный уровень владения навыком</span></div>
            <div class="legendvalue"><span class="legendcolor colordone"></span><span class="legendtitle">Проявленный уровень владения навыком</span></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        var result = AR.management,
            r = Math.round,
            renderer = function(v) { return v + '%'; };

        for (var i = 1; i < 4; i++) { //8
            new charts.Bar(
                '.management-' + i,
                r(result[i] && result[i].total ? result[i].total : 0),
                { valueRenderer: renderer }
            );
        }

        $('.managerial-skills').html(r(result.total || 0) + '%');
    });
</script>