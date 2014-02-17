<?php /** @var DeclineExplanation $declineExplanation */ ?>

<!-- blackout -->
<div class=""></div>

<!-- form form-decline-explanation -->
<div class="">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'     => 'form-decline-explanation',
        //'htmlOptions' => ['data-url' => $action],
        'action' => $action,
        //'enableAjaxValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'afterValidate'    => 'js:addDeclineReasonValidation',
        )

    )); ?>

    <?php echo $form->hiddenField($declineExplanation, 'invite_id'); ?>
    <?php echo $form->error($declineExplanation, 'invite_id'); ?>

    <h3>
        Пожалуйста, укажите причину отказа
    </h3>

    <span class="locator-form-fields">
        <div class="row <?= $this->hasErrors($form, $declineExplanation, 'reason_id'); ?>">
            <span class="error-place">
                <?php echo $form->error($declineExplanation, 'reason_id'); ?>
            </span>
            <?php echo $form->RadioButtonList($declineExplanation, 'reason_id', $reasons); ?>
        </div>

        <div class="row <?= $this->hasErrors($form, $declineExplanation, 'description'); ?>">
            <span class="error-place">
                <?php echo $form->error($declineExplanation, 'description') ?>
            </span>
            <?php echo $form->textArea($declineExplanation, 'description', [
                'placeholder'=>Yii::t("site","Failure cause")
            ]); ?>
        </div>
    </span>

    <div class="row">
        <!-- chancel-decline -->
        <span class = 'label background-dark-blue icon-circle-with-blue-arrow-big
            button-standard icon-padding-standard inter-active
            action-close-popup'>
            <?= $user->isAuth() ? 'Вернуться к приглашению' : 'Вернуться к регистрации' ?>
        </span>

        <!-- confirm-decline -->
        <span class = 'label background-dark-blue icon-circle-with-blue-arrow-big
            button-standard icon-padding-standard inter-active
            action-confirm-decline'>
            Отказаться
        </span>
    </div>
    <?php $this->endWidget(); ?>
</div>

<div class="locator-box-for-validation-response hide"></div>

