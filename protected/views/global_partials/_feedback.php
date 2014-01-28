<?php
//$cs = Yii::app()->clientScript;
//$assetsUrl = $this->getAssetsUrl();
// $cs->registerLessFile($assetsUrl . "/less/feedback.less", $assetsUrl . '/compiled_css/feedback.css');
?>

<div class="locator-feedback-dialog" style="display: none;">
    <?php
    $form = $this->beginWidget('CActiveForm', [
        'id'          => 'feedback-form',
        'htmlOptions' => ['class' => 'column-1-1'],
        'action'      => Yii::app()->request->hostInfo . '/static/feedback',
        'enableAjaxValidation' => true,
        'clientOptions'        => [
            'validateOnSubmit' => true,
            'validateOnChange' => true,
            'afterValidate'    => 'js:feedbackSubmit',
        ]
    ]);

    $model = new Feedback();
    $themes = Feedback::getFeedbackSubjects();
    ?>

    <h3 class="feedback-dialog-title pull-content-center"><?= Yii::t('site', 'Please contact us') ?></h3>

    <div class="row">
        <span class="error-place">
            <?php echo $form->error($model, 'theme'); ?>
        </span>
        <?php echo $form->dropDownList($model, 'theme', $themes, []); ?>
    </div>

    <div class="row unstandard-message-row">
        <span class="error-place">
            <?php echo $form->error($model, 'message'); ?>
        </span>
        <?php echo $form->textArea($model, 'message', [
            'cols'  => '40',
            'rows'  => '6',
            'maxlength' => 1500
        ]); ?>
    </div>

    <div class="pull-content-left">
        <span class="error-place">
            <?php echo $form->error($model, 'email'); ?>
        </span>
        <?php echo $form->textField($model, 'email', ['class' => 'inputs-wide-height']); ?>
        <?php echo CHtml::submitButton(Yii::t('site', 'Send'), [
            'class' => 'label background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard',
            'name'  => 'submit'
        ]); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>