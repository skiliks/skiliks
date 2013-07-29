<!--product-->
<article class="product">
    <hgroup class="font-white">
        <h1 class="page-header text-center"><?php echo Yii::t('site', 'About the Product')  ?></h1>
        <h3 class="font-normal text-center"><?php echo Yii::t('site', 'We have created on-line business simulation to discover manager’s skills.')  ?></h3>
    </hgroup>

        <div class="container-3 font-white">
            <div class="grid1 text-center"><?php echo Yii::t('site', 'Assessor needs no more than 5 minutes to appoint the assessment session and get the results. Detailed and comprehensive report on productivity, demonsrated managerial skills and professional qualities will be provided for each assessed candidate.')  ?></div>
            <div class="grid1 text-center"><?php echo Yii::t('site', 'Applicant needs 2-3 hours to get  through the exciting gameplay allowing a deep immersion into real working environment with managerial tasks and decision-making situations.')  ?></div>
            <div class="grid1 text-center"><?php echo Yii::t('site', 'Assessment outcome of a particular person can be compared with the outcomes of other people with different backgrounds, geographies, etc. This option is available for either assessors or applicants.')  ?></div>
        </div>

    <!-- NEW CONTENT -->
    <div class="textcener"><h2 class="total"><?php echo Yii::t('site', 'Overall manager’s rating')  ?></h2></div>
    <div class="allsummry">
        <div class="estmtresults">
            <div class="overall">
                <span class="allratebg"><span class="allrating" style="width:100%"></span></span> <span class="blockvalue"><span class="value"></span>%</span>
                <div class="allseprtwrap">
                    <div class="ratepercnt uprnavprcnt">50%</div>
                    <div class="ratepercnt resultprcnt">30%</div>
                    <div class="ratepercnt timeprcnt">20%</div>
                </div>
            </div>
        </div><!-- /estmtresults -->
        <div class="estmtileswrap">
            <div class="widthblock"><h2><?php echo Yii::t('site', 'Managerial skills')  ?></h2></div><!--<span class="signmore"></span></a></h2></div> -->
            <div class="widthblock"><h2><?php echo Yii::t('site', 'Productivity')  ?></h2></div>
            <div class="widthblock"><h2><?php echo Yii::t('site', 'Time management effectiveness')  ?></h2></div>
            <div class="widthblock lastwidthblock"><h2><?php echo Yii::t('site', 'Personal skills')  ?></h2></div>
        </div><!-- /estmtileswrap -->
    </div>
    <div class="clearfix maincharts">

        <div class="product-gauge-charts"></div>
        <div class="static-bullet-charts"></div><!-- product-bullet-charts -->
        {literal}
            <script type="text/javascript">
                var r = Math.round;

                new charts.Gauge('.product-gauge-charts', r(80), {class: 'inline'});
                new charts.Gauge('.product-gauge-charts', r(80), {class: 'inline'});
                new charts.Gauge('.product-gauge-charts', r(80), {class: 'inline'});

                new charts.Bullet('.product-bullet-charts', 50, {class: 'small'});
                new charts.Bullet('.product-bullet-charts', 70, {class: 'small'});
                new charts.Bullet('.product-bullet-charts', 40, {class: 'small'});

            </script>
        {/literal}
    </div>

    <div class="levellabels">
        <div class="widthblock"><h3><?php echo Yii::t('site', 'Level of skills maturity')  ?></h3></div>
        <div class="widthblock"><h3><?php echo Yii::t('site', 'Achievement of results: number and value of tasks completed')  ?></h3></div>
        <div class="widthblock"><h3><?php echo Yii::t('site', 'Speed of getting results')  ?></h3></div>
        <div class="widthblock lastwidthblock"><h3><?php echo Yii::t('site', 'Personal qualities demonstrated along the simulation')  ?></h3></div>
    </div>
    <div class="rateslist">
        <div class="widthblock"><h3><?php echo Yii::t('site', 'Scope of assessed skills')  ?></h3>
            <ol class="bluelist">
                <li class="hassubmenu"><a class="sub-menu-switcher" href="#managerial-skills-1-2" data-parent="managerial-skills"><?php echo Yii::t('site', 'Sticks to goals and priorities')  ?></a>
                    <ul class="productsubmenu">
                        <li><a href="#"><?php echo Yii::t('site', 'Sticks to company goals')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Follows personal priorities')  ?></a></li>
                    </ul>
                </li>
                <li class="hassubmenu">
                    <a class="sub-menu-switcher" href="#managerial-skills-1-2" data-parent="managerial-skills"><?php echo Yii::t('site', 'Manages tasks effectively')  ?></a>
                    <ul class="productsubmenu">
                        <li><a href="#"><?php echo Yii::t('site', 'Uses planning during the day')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Correctly defines tasks’ priorities while planning')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Follows tasks priorities in execution')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Completes tasks in full')  ?></a></li>
                    </ul>
                </li>
                <li class="hassubmenu"><a class="sub-menu-switcher" href="#managerial-skills-3-4" data-parent="managerial-skills"><?php echo Yii::t('site', 'Manages people effectively')  ?></a>
                    <ul class="productsubmenu">
                        <li><a href="#"><?php echo Yii::t('site', 'Uses delegation to manage scope of work')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Effectively manages resources with different qualification')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Uses feedback')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Delegates tasks to optimal employees')  ?></a></li>
                    </ul>
                </li>
                <li class="hassubmenu"><a class="sub-menu-switcher" href="#managerial-skills-3-4" data-parent="managerial-skills"><?php echo Yii::t('site', 'Wisely uses means of communication')  ?></a>
                    <ul class="productsubmenu">
                        <li><a href="#"><?php echo Yii::t('site', 'Wisely uses emails')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Wisely uses phone calls')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Wisely uses meetings')  ?></a></li>
                    </ul>
                </li>
                <li class="hassubmenu"><a class="sub-menu-switcher" href="#managerial-skills-5-6" data-parent="managerial-skills"><?php echo Yii::t('site', 'Deals with mail effectively')  ?></a>
                    <ul class="productsubmenu">
                        <li><a href="#"><?php echo Yii::t('site', 'Manages time spent on emails')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Effectively processes incoming emails')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Creates informative and short messages')  ?></a></li>
                    </ul>
                </li>
                <li class="hassubmenu"><a class="sub-menu-switcher" href="#managerial-skills-5-6" data-parent="managerial-skills"><?php echo Yii::t('site', 'Deals with calls effectively')  ?></a>
                    <ul class="productsubmenu">
                        <li><a href="#"><?php echo Yii::t('site', 'Manages time spent on phone calls')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Reasonably answers incoming calls')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Effectively processes incoming calls')  ?></a></li>
                    </ul>
                </li>
                <li class="hassubmenu"><a class="sub-menu-switcher" href="#managerial-skills-7" data-parent="managerial-skills"><?php echo Yii::t('site', 'Deals with meetings effectively')  ?></a>
                    <ul class="productsubmenu">
                        <li><a href="#"><?php echo Yii::t('site', 'Manages time spent on meetings')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Reasonably accepts visits')  ?></a></li>
                        <li><a href="#"><?php echo Yii::t('site', 'Effectively processes meeting outcomes')  ?></a></li>
                    </ul>
                </li>
            </ol>
        </div>
        <div class="widthblock"></div>
        <div class="widthblock"><h3><?php echo Yii::t('site', 'Indicators')  ?></h3>
            <ul class="bluelist nobultslist">
                <li><a class="productlink" href="#time-management-detail" data-parent="time-management"><?php echo Yii::t('site', 'Time distribution')  ?></a></li>
                <li><a class="productlink" href="#time-management"><?php echo Yii::t('site', 'Extra working hours') ?></a></li>
            </ul>
        </div>
        <div class="widthblock"><h3><?php echo Yii::t('site', 'Scope of measured qualities')  ?></h3>
            <ul class="bluelist nobultslist">
                <li><a class="productlink" href="#personal-qualities"><?php echo Yii::t('site', 'Results-orientation')  ?></a></li>
                <li><a class="productlink" href="#personal-qualities"><?php echo Yii::t('site', 'Attentiveness')  ?></a></li>
                <li><a class="productlink" href="#personal-qualities"><?php echo Yii::t('site', 'Responsibility')  ?></a></li>
                <li><a class="productlink" href="#personal-qualities"><?php echo Yii::t('site', 'Resistance to manipulation')  ?></a></li>
                <li><a class="productlink" href="#personal-qualities"><?php echo Yii::t('site', 'Flexibility')  ?></a></li>
                <li><a class="productlink" href="#personal-qualities"><?php echo Yii::t('site', 'Decision-making')  ?></a></li>
                <li><a class="productlink" href="#personal-qualities"><?php echo Yii::t('site', 'Speed of work')  ?></a></li>
                <li><a class="productlink" href="#personal-qualities"><?php echo Yii::t('site', 'Stress-resistance')  ?></a></li>
            </ul>
        </div>
    </div>
    <!-- /NEW CONTENT -->
    <section>
        <hgroup>
            <h3><?php echo Yii::t('site', 'More Information')  ?></h3>
            <h6><?php echo Yii::t('site', 'Our simulation is the easiest and most reliable way to discover your people management skills:')  ?></h6>
        </hgroup>

        <table>
            <col />
            <col />
            <tr>
                <th><h5><?php echo Yii::t('site', 'Easiest')  ?></h5></th>
                <th><h5><?php echo Yii::t('site', 'Most Reliable')  ?></h5></th>
            </tr>
            <tr>
                <td>
                    <h6><?php echo Yii::t('site', 'Saves your time')  ?></h6>
                    <p><?php echo Yii::t('site', 'Add up the number of hours you waste on the futile  interviews. Use these hours on your first priority project!')  ?></p>
                    <p><?php echo Yii::t('site', 'Two clicks to start the process and get the necessary assessment.')  ?></p>
                    <h6><?php echo Yii::t('site', 'Unlimited number of applicants in any part of the world.')  ?></h6>
                    <p><?php echo Yii::t('site', 'No limits! Use the simulation for managers from anywhere in the world. Assess as many people as you need.')  ?></p>
                    <h6><?php echo Yii::t('site', 'No hard-, soft- or any-ware required! Just make  sure you and your managers are on line!')  ?></h6>
                    <p><?php echo Yii::t('site', '<strong>NO NEED</strong> to  buy computers')  ?></p>
                    <p><?php echo Yii::t('site', '<strong>NO NEED</strong> to buy/distribute/integrate any soft')  ?></p>
                    <p><?php echo Yii::t('site', '<strong>NO NEED</strong> to lease additional office space')  ?></p>
                    <h6><?php echo Yii::t('site', 'Results can be used immediately')  ?></h6>
                    <p><?php echo Yii::t('site', '<strong>NO NEED</strong> to call experts to interpret the results! Just open your Skiliks Office and use the managers’ assessment data to make the decision!')  ?></p>
                </td>
                <td>
                    <h6><?php echo Yii::t('site', 'Focused on the skills')  ?></h6>
                    <p><?php echo Yii::t('site', 'We focus the simulation on measuring  real managerial skills – their ability to bring value from the first working day. Skill itself is the mastered capacity to carry out pre-determined results with minimum resources.')  ?></p>
                    <h6><?php echo Yii::t('site', 'Based on best working practices')  ?></h6>
                    <p><?php echo Yii::t('site', 'We developed the assessment based on the selection of crucial practical skills that define manager’s performance with detailed analysis of how these skills become apparent in behaviour')  ?></p>
                    <p><?php echo Yii::t('site', 'We are continuously up-grading managerial skills profile')  ?></p>
                    <h6><?php echo Yii::t('site', 'Uses real work environment, tasks and decision making situations')  ?></h6>
                    <p><?php echo Yii::t('site', 'In the simulation we have replicated the manager’s everyday life - familiar tasks, situations, interfaces and office environment. It helps manager to be himself and demonstrate his best results.')  ?></p>
                    <h6><?php echo Yii::t('site', 'Based on mathematical methods not just feelings')  ?></h6>
                    <p><?php echo Yii::t('site', 'Each skill is assessed in many instances throughout the simulation forming valid outcome. Just imagine how many days you need in order to do it in real life!')  ?></p>
                    <p><?php echo Yii::t('site', 'We collect and analyse the data of hundreds of participants to manage the simulation.')  ?></p>
                    <p><?php echo Yii::t('site', 'We give you the possibility to compare candidates using clear quantitative criteria.')  ?></p>
                </td>
            </tr>
        </table>
    </section>
</article>
<!--product end-->