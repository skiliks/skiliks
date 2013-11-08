<?php
$isPersonal = $account_type === 'personal';
?>
<script type="text/javascript">$(function () {
        $('input').focus(function () {
            $('.form').removeClass('active');
            $(this).parents('.form').addClass('active');
        })
    })</script>
<section class="registration">
    <h2 class="shorter-title"><?php echo empty($simPassed) ? 'Зарегистрируйтесь, выбрав подходящий профиль' : 'Зарегистрируйтесь, выбрав подходящий профиль, и получите пример отчёта' ?></h2>
    <div class="form form-account-personal" style="background-color: <?= (!$isPersonal)?'#fdfbc6':'rgb(254,227,116)'?>">
        <a class="regicon <?= ($isPersonal)?'icon-check':'icon-chooce'?> registration_check" href="#">
            <span style="display: <?= ($isPersonal)?'none':'block'?>" class="choose-account-button-span">
                <?php echo Yii::t('site', 'Выбрать');?>
            </span>
        </a>
        <h1>Индивидуальный<br>профиль</h1>
        <p class="p-chose-account-type ProximaNova-Bold">(Вы - сотрудник или соискатель)</p>
        <ul>
            <li class="ProximaNova-Bold"><?php echo Yii::t('site', 'Возможность получать приглашения от работодателя') ?></li>
            <li class="ProximaNova-Bold"><?php echo Yii::t('site', 'Полная версия по приглашению') ?></li>
            <li class="ProximaNova-Bold"><?php echo Yii::t('site', 'Демо-версия бесплатно') ?></li>
        </ul>
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id'                   => 'yum-user-registration-form',
            'enableAjaxValidation' => false,
        )); ?>
        <div class="row">
            <?php echo $form->error($accountPersonal, 'professional_status_id', ["class" => "errorMessage general_error registration-industry-error"]); ?>
            <div class="field registration-personal-additional-field">
                <?php echo $form->labelEx($accountPersonal     ,'professional_status_id', ["class" => "ProximaNova-Bold"]); ?>
                <?php echo $form->dropDownList($accountPersonal,'professional_status_id', $statuses); ?>
            </div>
        </div>
        <div class="row"></div>
    </div>
    <!-- --------------------------------------------------------------------------------------------------------- -->
    <div class="form form-account-corporate" style="background-color: <?= (!$isPersonal)?'rgb(254,227,116)':'#fdfbc6'?>">
        <a class="regicon <?= ($isPersonal)?'icon-chooce':'icon-check'?> registration_check" href="#">
            <span style="display: <?= ($isPersonal)?'block':'none'?>;" class="choose-account-button-span">
                <?php echo Yii::t('site', 'Выбрать');?>
            </span>
        </a>
        <h1>Корпоративный<br>профиль</h1>
        <p class="p-chose-account-type ProximaNova-Bold">(Вы - работодатель)</p>
        <ul class="registration-corporate-benefits">
            <li class="ProximaNova-Bold"><?php echo Yii::t('site', '3 симуляции бесплатно (Полная версия)') ?></li>
            <li class="ProximaNova-Bold"><?php echo Yii::t('site', 'Пакет симуляций для оценки кандидатов и сотрудников') ?></li>
            <li class="ProximaNova-Bold"><?php echo Yii::t('site', 'Удобный инструмент для прогресса оценки') ?></li>
        </ul>
        <div class="row">
            <?php echo $form->error($accountCorporate, 'industry_id', ["class" => "errorMessage general_error registration-industry-error"]); ?>
            <div class="field">
                <?php echo $form->labelEx($accountCorporate     , 'industry_id', ["class" => "ProximaNova-Bold"]); ?>
                <?php echo $form->dropDownList($accountCorporate, 'industry_id', $industries); ?>
            </div>
        </div>
    </div>
    <!-- --------------------------------------------------------------------------------------------------------- -->
    <div style="clear:both;"></div>
</section>

    <?php echo $form->error($profile, 'not_activated', ['class'=>'registration-general-error globalErrorMessage emailIsExistAndNotActivated ProximaNova']); ?>
    <?php echo $form->error($profile, 'is_baned', ['class'=>'registration-general-error globalErrorMessage emailIsExistAndNotActivated ProximaNova']); ?>

<?php if($display_results_for) : ?>
    <?php $this->renderPartial('//global_partials/_popup_result_simulation_container', [ 'display_results_for' => $display_results_for]) ?>
<?php endif; ?>

<div class="form registrationform">
    <div class="transparent-boder">
        <div class="row one-part">
            <div class="row two-parts">
                <?php echo $form->textField($profile, 'firstname', ['placeholder' => $profile->getAttributeLabel('firstname')]); ?>
                <?php echo $form->error($profile, 'firstname'); ?>
            </div>

            <div class="row two-parts to-right">
                <?php echo $form->textField($profile, 'lastname', ['placeholder' => $profile->getAttributeLabel('lastname')]); ?>
                <?php echo $form->error($profile, 'lastname'); ?>
            </div>
        </div>
        <div class="row one-part to-left">
            <div class="row four-parts email-input">
                <?php echo $form->textField($profile, 'email', ['placeholder' => $profile->getAttributeLabel('email')]); ?>
                <?php echo $form->error($profile, 'email'); ?>
            </div>

            <div class="row four-parts to-right password-input">
                <?php echo $form->passwordField($user, 'password', ['placeholder' => $user->getAttributeLabel('password')]); ?>
                <?php echo $form->error($user, 'password'); ?>
            </div>

            <div class="row four-parts to-right password-again-input">
                <?php echo $form->passwordField($user, 'password_again', ['placeholder' => $user->getAttributeLabel('password_again')]); ?>
                <?php echo $form->error($user, 'password_again'); ?>
            </div>
            <div class="row" id="account-type" style="display: none">
                <input type="hidden" value="<?=$account_type?>" name="account-type">
            </div>
            <div class="row four-parts to-right submit-input">
                <button type="submit" class="ProximaNova-Bold blue-submit-button registration-button"><?= Yii::t("site","Sign up") ?></button>
            </div>
        </div>
        <div style="clear:both;"></div>
        <?php  echo $form->error($user, 'general_error', ['class'=>'errorMessage general_error']); ?>
        <?php //echo $form->error($profile, 'general_error', ['style'=>'position:static; display:inline-block; margin-top:5px; float:left;']); ?>

    </div>

    <div class="reg terms-confirm"><?= $form->error($user, 'agree_with_terms'); ?><?= $form->checkBox($user, 'agree_with_terms', ['value' => 'yes', 'uncheckValue' => null]); ?>
        <?= $form->labelEx($user, 'agree_with_terms', ['label' => 'Я принимаю <a href="#" class="terms">Условия и Лицензионное соглашение</a>']); ?>
    </div>
    <?php $this->endWidget(); ?>
</div>
