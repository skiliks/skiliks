<div class="extrasidepads managerialmain">
    <div class="textcener"><h2 class="total totalwithresult">Управленческие навыки <span class="value blockvalue">0%</span></h2></div>
    <div class="clearfix">
        <div class="labeltitles">
            <h3>По группам навыков</h3>
            <h4>Критичные навыки</h4>
        </div>
        <p class="barstitle mangrlresultstitle">Уровень владения навыком</p>
    </div>

    <div class="clearfix mangrlresults">
        <div class="labels">
            <p class="labelwrap"><span class="thelabel"><a href="#managerial-skills-1-2">1. Следование приоритетам <span class="signmore"></span></a></span></p>
            <p class="labelwrap"><span class="thelabel"><a href="#managerial-skills-1-2">2. Управление задачами <span class="signmore"></span></a></span></p>
            <p class="labelwrap"><span class="thelabel"><a href="#managerial-skills-3-4">3. Управление людьми <span class="signmore"></span></a></span></p>
            <p class="labelwrap"><span class="thelabel labelhack"><a href="#managerial-skills-3-4">4. Оптимальный выбор каналов коммуникации <span class="signmore"></span></a></span></p>
        </div>
        <div class="barswrap main-skills">
            <div class="chartbar management-1"></div>
            <div class="chartbar management-2"></div>
            <div class="chartbar management-3"></div>
            <div class="chartbar management-4"></div>
        </div>
    </div>
    <div class="clearfix">
        <div class="labeltitles toplabelmargin">
            <h4>Другие важные навыки</h4>
        </div>
    </div>
    <div class="clearfix mangrlresults">
        <div class="labels">
            <p class="labelwrap"><span class="thelabel"><a href="#managerial-skills-5-6">5. Эффективная работа с почтой <span class="signmore"></span></a></span></p>
            <p class="labelwrap"><span class="thelabel"><a href="#managerial-skills-5-6">6. Эффективное управление звонками <span class="signmore"></span></a></span></p>
            <p class="labelwrap"><span class="thelabel"><a href="#managerial-skills-7">7. Эффективное управление встречами <span class="signmore"></span></a></span></p>
        </div>
        <div class="barswrap otherskills">
            <div class="chartbar management-5"></div>
            <div class="chartbar management-6"></div>
            <div class="chartbar management-7"></div>
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

        for (var i = 1; i < 8; i++) {
            new charts.Bar(
                '.management-' + i,
                r(result[i] && result[i].total ? result[i].total : 0),
                { valueRenderer: renderer }
            );
        }
    });
</script>