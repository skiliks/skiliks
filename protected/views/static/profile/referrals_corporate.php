
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">
    <?php $this->renderPartial('_menu_corporate', ['active' => ['referrals' => true]]) ?>

    <div class="profileform radiusthree">
        <div class="total-rows">Всего приглашенных: <?=$totalReferrals ?></div><br/>
        <?php $this->renderPartial('_referrals_list', []) ?>

    </div>


</div>