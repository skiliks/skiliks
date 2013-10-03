<!--product-->
<article class="product">
    <hgroup>
        <h2>{Yii::t('site', 'About the Product')}</h2>
        <h6>{Yii::t('site', 'We have created on-line business simulation to discover manager’s skills.')}</h6>
    </hgroup>

    <table class="productfeatrs">
        <col />
        <col />
        <col />
        <tr>
            <td>{Yii::t('site', 'Assessor needs no more than 5 minutes to appoint the assessment session and get the results. Detailed report on productivity, demonsrated managerial skills will be provided for each assessed candidate.')}</td>
            <td>{Yii::t('site', 'Applicant needs 2-3 hours to get  through the exciting gameplay allowing a deep immersion into real working environment with managerial tasks and decision-making situations.')}</td>
            <td>{Yii::t('site', 'Assessment outcome of a particular person can be compared with the outcomes of other people with different backgrounds, geographies, etc. This option is available for either assessors or applicants.')}</td>
        </tr>
    </table>

    <!-- NEW CONTENT -->
    <div class="textcener"><h2 class="total">{Yii::t('site', 'Overall manager’s rating')}</h2></div>
    <div class="allsummry">
        <div class="estmtresults">
            <div class="overall percentil_overall_container percentil_overall_container_product">
            <span class="percentil_base">
                <span class="percentil_overall" style="width:80%"></span>
            </span>
                <div class="percentil_text_product">P</div>
            </div>
            <div class="clear: both"></div>
            <div class="overall">
                <span class="allratebg"><span class="allrating" style="width:100%"></span></span> <span class="blockvalue"><span class="value">%</span></span>
            </div>
        </div><!-- /estmtresults -->
        <div class="estmtileswrap">
            <div class="widthblock"><h2>{Yii::t('site', 'Managerial skills')}</h2></div><!--<span class="signmore"></span></a></h2></div> -->
            <div class="widthblock"><h2>{Yii::t('site', 'Productivity')}</h2></div>
            <div class="widthblock"><h2>{Yii::t('site', 'Time management effectiveness')}</h2></div>
        </div><!-- /estmtileswrap -->
    </div>
    <div class="clearfix maincharts">

        <div class="product-gauge-charts"></div>
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
        <div class="widthblock"><h3>{Yii::t('site', 'Level of skills maturity')}</h3></div>
        <div class="widthblock"><h3>{Yii::t('site', 'Achievement of results: number and value of tasks completed')}</h3></div>
        <div class="widthblock"><h3 class="product_h3_margin_left">{Yii::t('site', 'Speed of getting results')}</h3></div>
    </div>
    <div class="rateslist">
        <div class="widthblock"><h3>{Yii::t('site', 'Scope of assessed skills')}</h3>
            <ol class="bluelist">
                <li class="hassubmenu"><a class="sub-menu-switcher" href="#managerial-skills-1-2" data-parent="managerial-skills">{Yii::t('site', 'Manages task in accordance with priorities')}</a>
                    <ul class="productsubmenu">
                        <li><a href="#">{Yii::t('site', 'Defines priorities')}</a></li>
                        <li><a href="#">{Yii::t('site', 'Uses planning during the day')}</a></li>
                        <li><a href="#">{Yii::t('site', 'Correctly defines tasks’ priorities while planning')}</a></li>
                        <li><a href="#">{Yii::t('site', 'Follows tasks priorities in execution')}</a></li>
                        <li><a href="#">{Yii::t('site', 'Completes tasks in full')}</a></li>
                    </ul>
                </li>
                <li class="hassubmenu">
                    <a class="sub-menu-switcher" href="#managerial-skills-1-2" data-parent="managerial-skills">{Yii::t('site', 'Manages people effectively')}</a>
                    <ul class="productsubmenu">
                        <li><a href="#">{Yii::t('site', 'Uses delegation to manage scope of work')}</a></li>
                        <li><a href="#">{Yii::t('site', 'Effectively manages resources with different qualification')}</a></li>
                        <li><a href="#">{Yii::t('site', 'Uses feedback')}</a></li>
                    </ul>
                </li>
                <li class="hassubmenu"><a class="sub-menu-switcher" href="#managerial-skills-3-4" data-parent="managerial-skills">{Yii::t('site', 'Communicates effectiely')}</a>
                    <ul class="productsubmenu">
                        <li><a href="#">{Yii::t('site', 'Wisely uses means of communication')}</a></li>
                        <li><a href="#">{Yii::t('site', 'Deals with mail effectively')}</a></li>
                        <li><a href="#">{Yii::t('site', 'Deals with calls effectively')}</a></li>
                        <li><a href="#">{Yii::t('site', 'Deals with meetings effectively')}</a></li>
                    </ul>
                </li>
            </ol>
        </div>
        <div class="widthblock"><h3>{Yii::t('site', 'Indicators')}</h3>
            <ul class="bluelist nobultslist">
                <li><a class="productlink" href="#time-management-detail" data-parent="time-management">{Yii::t('site', 'Productivity')}</a></li>
            </ul>
        </div>
        <div class="widthblock"><h3>{Yii::t('site', 'Indicators')}</h3>
            <ul class="bluelist nobultslist">
                <li><a class="productlink" href="#time-management-detail" data-parent="time-management">{Yii::t('site', 'Time distribution')}</a></li>
                <li><a class="productlink" href="#time-management">{Yii::t('site', 'Extra working hours')} </a></li>
            </ul>
        </div>
    </div>
    <!-- /NEW CONTENT -->
    <section>
        <hgroup>
            <h2>{Yii::t('site', 'More Information')}</h2>
            <h6>{Yii::t('site', 'Our simulation is easy and reliable way to discover your people management skills:')}</h6>
        </hgroup>

        <table>
            <col />
            <col />
            <tr>
                <th><h5>{Yii::t('site', 'Easy')}</h5></th>
                <th><h5>{Yii::t('site', 'Reliable')}</h5></th>
            </tr>
            <tr>
                <td>
                    <h6>{Yii::t('site', 'Saves your time')}</h6>
                    <p>{Yii::t('site', 'Add up the number of hours you waste on the futile  interviews. Use these hours on your first priority project!')}</p>
                    <p>{Yii::t('site', 'Two clicks to start the process and get the necessary assessment.')}</p>
                    <h6>{Yii::t('site', 'Unlimited number of applicants in any part of the world.')}</h6>
                    <p>{Yii::t('site', 'No limits! Use the simulation for managers from anywhere in the world. Assess as many people as you need.')}</p>
                    <h6>{Yii::t('site', 'No hard-, soft- or any-ware required! Just make  sure you and your managers are on line!')}</h6>
                    <p>{Yii::t('site', '<strong>NO NEED</strong> to  buy computers')}</p>
                    <p>{Yii::t('site', '<strong>NO NEED</strong> to buy/distribute/integrate any soft')}</p>
                    <p>{Yii::t('site', '<strong>NO NEED</strong> to lease additional office space')}</p>
                    <h6>{Yii::t('site', 'Results can be used immediately')}</h6>
                    <p>{Yii::t('site', '<strong>NO NEED</strong> to call experts to interpret the results! Just open your Skiliks Office and use the managers’ assessment data to make the decision!')}</p>
                </td>
                <td>
                    <h6>{Yii::t('site', 'Focused on the practical skills')}</h6>
                    <p>{Yii::t('site', 'We focus the simulation on measuring  real managerial skills – their ability to bring value from the first working day. Skill itself is the mastered capacity to carry out pre-determined results with minimum resources.')}</p>
                    <h6>{Yii::t('site', 'Based on best working practices')}</h6>
                    <p>{Yii::t('site', 'We developed the assessment based on the selection of crucial practical skills that define manager’s performance with detailed analysis of how these skills become apparent in behaviour')}</p>
                    <p>{Yii::t('site', 'We are continuously up-grading managerial skills profile')}</p>
                    <h6>{Yii::t('site', 'Uses real work environment, tasks and decision making situations')}</h6>
                    <p>{Yii::t('site', 'In the simulation we have replicated the manager’s everyday life - familiar tasks, situations, interfaces and office environment. It helps manager to be himself and demonstrate his best results.')}</p>
                    <h6>{Yii::t('site', 'Based on mathematical methods not just feelings')}</h6>
                    <p>{Yii::t('site', 'Each skill is assessed in many instances throughout the simulation forming valid outcome. Just imagine how many days you need in order to do it in real life!')}</p>
                    <p>{Yii::t('site', 'We collect and analyse the data of hundreds of participants to manage the simulation.')}</p>
                    <p>{Yii::t('site', 'We give you the possibility to compare candidates using clear quantitative criteria.')}</p>
                </td>
            </tr>
        </table>
    </section>
</article>
<!--product end-->