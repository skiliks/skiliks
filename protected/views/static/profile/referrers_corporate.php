
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">
    <?php $this->renderPartial('_menu_corporate', ['active' => ['referrers' => true]]) ?>

    <div class="profileform radiusthree">

        <?php $this->renderPartial('_reffers_list', []) ?>


    </div>


</div>