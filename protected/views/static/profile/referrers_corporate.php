
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">
    <?php $this->renderPartial('_menu_corporate', ['active' => ['referrers' => true]]) ?>

    <div class="profileform radiusthree">
        <div class="total-rows">Всего приглашенных: <?=$totalRefers ?></div><br/>
        <?php $this->renderPartial('_reffers_list', []) ?>

    </div>


</div>