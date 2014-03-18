<!--product-->
<article class="product">

<hgroup>
    <h1 class="pull-content-center">
        <?= Yii::t('site', 'About the Product')?>
    </h1>
    <br/>
    <h4 class="pull-content-center">
        <?= Yii::t('site', 'We have created on-line business simulation to discover manager’s skills.')?>
    </h4>
</hgroup>
<br/>

<div class="column-full color-ffffff">
        <span class="column-1-3 pull-content-center us-column-1-3">
            <?= Yii::t('site', 'Assessor needs no more than 5 minutes to appoint the assessment session and get the results. Detailed report on productivity, demonsrated managerial skills will be provided for each assessed candidate.')?>
        </span>
    <span class="us-separator"></span>
        <span class="column-1-3 pull-content-center us-column-1-3">
            <?= Yii::t('site', 'Applicant needs 2-3 hours to get  through the exciting gameplay allowing a deep immersion into real working environment with managerial tasks and decision-making situations.')?>
        </span>
    <span class="us-separator"></span>
        <span class="column-1-3 pull-content-center us-column-1-3">
            <?= Yii::t('site', 'Assessment outcome of a particular person can be compared with the outcomes of other people with different backgrounds, geographies, etc. This option is available for either assessors or applicants.')?>
        </span>
</div>
<br/>

<!-- NEW CONTENT -->
<div class="us-overall">
    <h2 class="pull-content-center">
        <?= Yii::t('site', 'Overall manager’s rating')?>
    </h2>
</div>
<div class="allsummry pull-content-center">
    <div class="estmtresults pull-content-center">
        <div class="overall percentil_overall_container percentil_overall_container_product">
            <span class="percentil_base">
                <span class="percentil_overall" style="width:80%"></span>
            </span>
            <span class="percentil_text_product">P</span>
        </div>
        <div class="clear: both"></div>
        <div class="overall">
                <span class="allratebg">
                    <span class="allrating" style="width:100%"></span>
                </span>
                <span class="blockvalue"
                    ><span class="value">%</span>
                </span>
        </div>
    </div><!-- /estmtresults -->

    <br/>

    <div class="estmtileswrap product-estmtileswrap  pull-content-center us-speedometers">
            <span class="widthblock column-1-3 vertical-align-top">
                <h4 class=" pull-content-center">
                    <?= Yii::t('site', 'Time management effectiveness')?>
                </h4>
            </span>
            <span class="widthblock column-1-3 vertical-align-top">
                <h4 class=" pull-content-center">
                    <?= Yii::t('site', 'Productivity')?>
                </h4>
            </span>
            <span class="widthblock column-1-3 vertical-align-top">
                <h4 class=" pull-content-center">
                    <?= Yii::t('site', 'Managerial skills')?>
                </h4>
            </span>
    </div><!-- /estmtileswrap -->
</div>
<div class="clearfix maincharts">
    <div class="product-gauge-charts us-product-gauge-charts"></div>
</div>

<div class="levellabels  pull-content-center us-levellabels">
        <span class="widthblock column-1-3 pull-content-center vertical-align-top">
            <h6>
                <?= Yii::t('site', 'Speed of getting results')?>
            </h6>
        </span>
        <span class="widthblock column-1-3 pull-content-center vertical-align-top">
            <h6>
                <?= Yii::t('site', 'Achievement of results: number and value of tasks completed')?>
            </h6>
        </span>
        <span class="widthblock column-1-3 pull-content-center vertical-align-top">
            <h6 class="product_h3_margin_left">
                <?= Yii::t('site', 'Level of skills maturity')?>
            </h6>
        </span>
</div>
<br/>
<div class="rateslist pull-content-center us-rateslist">
        <span class="widthblock column-1-3 vertical-align-top">
            <h4 class="pull-content-center"><?= Yii::t('site', 'Indicators')?></h4>
            <ol class="bluelist nobultslist">
                <li><a class="productlink" href="#time-management-detail" data-parent="time-management"><?= Yii::t('site', 'Time distribution')?></a></li>
                <li><a class="productlink" href="#time-management"><?= Yii::t('site', 'Extra working hours')?> </a></li>
            </ol>
        </span>
        <span class="widthblock column-1-3 vertical-align-top">
            <h4 class="pull-content-center"><?= Yii::t('site', 'Indicators')?></h4>
            <ol class="bluelist nobultslist">
                <li><a class="productlink" href="#time-management-detail" data-parent="time-management"><?= Yii::t('site', 'Productivity')?></a></li>
            </ol>
        </span>
        <span class="widthblock column-1-3 vertical-align-top">
            <h4 class="pull-content-center"><?= Yii::t('site', 'Scope of assessed skills')?></h4>
            <ol class="bluelist">
                <li class="hassubmenu">
                    <a class="sub-menu-switcher color-ffffff" href="#managerial-skills-1-2" data-parent="managerial-skills">
                        <?= Yii::t('site', 'Manages task in accordance with priorities')?>
                    </a>
                    <ul class="productsubmenu hide">
                        <li><a href="#"><?= Yii::t('site', 'Uses planning during the day')?></a></li>
                        <li><a href="#"><?= Yii::t('site', 'Correctly defines tasks’ priorities while planning')?></a></li>
                        <li><a href="#"><?= Yii::t('site', 'Follows tasks priorities in execution')?></a></li>
                        <li><a href="#"><?= Yii::t('site', 'Completes tasks in full')?></a></li>
                    </ul>
                </li>
                <li class="hassubmenu">
                    <a class="sub-menu-switcher color-ffffff" href="#managerial-skills-1-2" data-parent="managerial-skills">
                        <?= Yii::t('site', 'Manages people effectively')?>
                    </a>
                    <ul class="productsubmenu hide">
                        <li><a href="#"><?= Yii::t('site', 'Uses delegation to manage scope of work')?></a></li>
                        <li><a href="#"><?= Yii::t('site', 'Effectively manages resources with different qualification')?></a></li>
                        <li><a href="#"><?= Yii::t('site', 'Uses feedback')?></a></li>
                    </ul>
                </li>
                <li class="hassubmenu">
                    <a class="sub-menu-switcher color-ffffff" href="#managerial-skills-3-4" data-parent="managerial-skills">
                        <?= Yii::t('site', 'Communicates effectiely')?>
                    </a>
                    <ul class="productsubmenu hide">
                        <li><a href="#"><?= Yii::t('site', 'Wisely uses means of communication')?></a></li>
                        <li><a href="#"><?= Yii::t('site', 'Deals with mail effectively')?></a></li>
                        <li><a href="#"><?= Yii::t('site', 'Deals with calls effectively')?></a></li>
                        <li><a href="#"><?= Yii::t('site', 'Deals with meetings effectively')?></a></li>
                    </ul>
                </li>
            </ol>
        </span>
</div>
<!-- /NEW CONTENT -->
<br/>
<section>
    <hgroup>
        <h1 class="pull-content-center">
            <?= Yii::t('site', 'More Information')?>
        </h1>
        <br/>
        <h4 class="pull-content-center">
            <?= Yii::t('site', 'Our simulation is easy and reliable way to discover your people management skills:')?>
        </h4>
        <br/>
    </hgroup>

    <div class="column-full">
            <span class="column-1-2 pull-content-center vertical-align-top">
                <span class="us-nice-border nice-border us-first-column pull-right">
                    <span class="us-box-title background-yellow">
                        <h1 class="color-ffffff"><?= Yii::t('site', 'Easy')?></h1>
                    </span>
                    <span class="us-box-content background-yellow">
                        <h5><?= Yii::t('site', 'Saves your time')?></h5>

                        <p class="color-3D4041"><?= Yii::t('site', 'Add up the number of hours you waste on the futile  interviews. Use these hours on your first priority project!')?></p>
                        <p class="color-3D4041"><?= Yii::t('site', 'Two clicks to start the process and get the necessary assessment.')?></p>

                        <h5><?= Yii::t('site', 'Unlimited number of applicants in any part of the world.')?></h5>

                        <p class="color-3D4041"><?= Yii::t('site', 'No limits! Use the simulation for managers from anywhere in the world. Assess as many people as you need.')?></p>

                        <h5><?= Yii::t('site', 'No hard-, soft- or any-ware required! Just make  sure you and your managers are on line!')?></h5>

                        <p class="color-3D4041"><?= Yii::t('site', 'NO NEED to  buy computers')?></p>
                        <p class="color-3D4041"><?= Yii::t('site', 'NO NEED to buy/distribute/integrate any soft')?></p>
                        <p class="color-3D4041"><?= Yii::t('site', 'NO NEED to lease additional office space')?></p>

                        <h5><?= Yii::t('site', 'Results can be used immediately')?></h5>

                        <p class="color-3D4041"><?= Yii::t('site', 'NO NEED to call experts to interpret the results! Just open your Skiliks Office and use the managers’ assessment data to make the decision!')?></p>
                    </span>
                </span>
            </span>

        <!-- ######################################################################### -->

            <span class="column-1-2 pull-content-center vertical-align-top">
                <span class="us-nice-border nice-border us-second-column pull-left">
                    <span class="us-box-title background-yellow">
                        <h1 class="color-ffffff"><?= Yii::t('site', 'Reliable')?></h1>
                    </span>
                    <span class="us-box-content background-yellow">
                        <h5><?= Yii::t('site', 'Focused on the practical skills')?></h5>

                        <p class="color-3D4041"><?= Yii::t('site', 'We focus the simulation on measuring  real managerial skills – their ability to bring value from the first working day. Skill itself is the mastered capacity to carry out pre-determined results with minimum resources.')?></p>

                        <h5><?= Yii::t('site', 'Based on best working practices')?></h5>

                        <p class="color-3D4041"><?= Yii::t('site', 'We developed the assessment based on the selection of crucial practical skills that define manager’s performance with detailed analysis of how these skills become apparent in behaviour')?></p>
                        <p class="color-3D4041"><?= Yii::t('site', 'We are continuously up-grading managerial skills profile')?></p>

                        <h5><?= Yii::t('site', 'Uses real work environment, tasks and decision making situations')?></h5>

                        <p class="color-3D4041"><?= Yii::t('site', 'In the simulation we have replicated the manager’s everyday life - familiar tasks, situations, interfaces and office environment. It helps manager to be himself and demonstrate his best results.')?></p>

                        <h5><?= Yii::t('site', 'Based on mathematical methods not just feelings')?></h5>

                        <p class="color-3D4041"><?= Yii::t('site', 'Each skill is assessed in many instances throughout the simulation forming valid outcome. Just imagine how many days you need in order to do it in real life!')?></p>
                        <p class="color-3D4041"><?= Yii::t('site', 'We collect and analyse the data of hundreds of participants to manage the simulation.')?></p>
                        <p class="color-3D4041"><?= Yii::t('site', 'We give you the possibility to compare candidates using clear quantitative criteria.')?></p>
                    </span>
                </span>
            </span>
    </div>
</section>
</article>
<!--product end-->

<div class="clearfix"></div>