

<section class="page-title-box column-full pull-content-left ">
    <h1 class="bottom-margin-standard">
        <?php echo Yii::t('site', 'Profile') ?>
    </h1>
</section>

<section class="pull-content-left nice-border reset-padding
    border-radius-standard background-transparent-20 overflow-hidden">

    <aside class="column-1-3 inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_corporate', ['active' => ['referrals' => true]]) ?>
    </aside>

    <section class="column-2-3-wide inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side">
        <div class="">
            Всего приглашенных: <?=$totalReferrals ?>
            <br/>

            <?php $this->renderPartial('_referrals_list', ['dataProvider' => $dataProvider]) ?>

            <div class="table-footer"></div>
        </div>
    </section>
</section>

<div class="clearfix column-full"></div>

<!-- dialogReferralRejected -->
<div class="locator-for-ui-dialog-status-data hide">

</div>



