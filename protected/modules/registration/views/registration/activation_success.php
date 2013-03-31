<h2 class="thetitle longercontent text-center"><?php echo Yii::t('site', 'Your account has been activated'); ?> </h2>
<div class="form registrationform">
    <div class="transparent-boder">
        <div class="radiusthree yellowbg">
            <div class="registermessage registerpads">
                <a class="regicon <?php if($user->is_check == 1){ echo "icon-check"; }else{ echo "icon-chooce"; } ?>" id="registration_check" href="/simulation/promo/2"><span style="display: <?php if($user->is_check == 1){ echo "none";}else{ echo "block"; }?>"><?php echo Yii::t('site', 'Выбрать');?></a>
                <h3>Пробный тест</h3>
                <div class="testtime"><strong>45</strong> Минут</div>
                <ul>
                    <li>Частичная оценка навыков бесплатно</li>
                    <li>Погружение в игровую среду для понимания, как работает симуляция</li>
                    <li>Опыт прохождения теста</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<p class="text-center longercontent"> <?php Yum::t('Click {here} to go to the login form', array(
			'{here}' => CHtml::link(Yum::t('here'), Yum::module()->loginUrl
				))); ?> </p>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id'                   => 'yum-user-registration-form',
    'enableAjaxValidation' => false,
)); ?>
<div class="row" style="display: none">
    <?php echo $form->hiddenField($user, 'is_check'); ?>
</div>
<?php if((int)$user->is_check === 1){
    $btn_text = Yii::t('site', 'Начать');
}else{
    $btn_text = Yii::t('site', 'Далее');
} ?>
<div class="row text-center longercontent"><?php echo CHtml::submitButton($btn_text, ['class'=>'bigbtnsubmt', 'id'=>'registration_switch', 'data-next'=>Yii::t('site', 'Далее'), 'data-start'=>Yii::t('site', 'Начать')]); ?></div>

<?php $this->endWidget(); ?>
