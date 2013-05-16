<?php $cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();

$cs->registerLessFile($assetsUrl . "/less/feedback.less", $assetsUrl . '/compiled_css/feedback.css');
?>

<div id="feedback-dialog" style="display: none;">
    <h2 class="feedback-dialog-title"><?= Yii::t('site', 'Пожалуйста свяжитесь с нами') ?></h2>
    <?php
    $form = $this->beginWidget('CActiveForm', [
        'id' => 'feedback-form',
        'htmlOptions' => ['class' => 'feedback'],
        'action' => Yii::app()->request->hostInfo . '/static/feedback',
        'enableAjaxValidation' => true,
        'clientOptions' => array(
            'validateOnSubmit' => true,
            'afterValidate'=>'js:feedbackSubmit',
        )
    ]);

    $model = new Feedback();

    $themes = Feedback::getFeedbackSubjects();
    ?>

    <div class="form-all">
        <ul class="form-section">
            <li class="form-line">
                <?php echo $form->labelEx($model, 'theme', ['class' => 'form-label-left']); ?>
                <div class="form-input">
                    <?php echo $form->dropDownList($model, 'theme', $themes, ['class' => 'form-dropdown']); ?>
                </div>
                <?php echo $form->error($model, 'theme'); ?>
            </li>
            <li class="form-line">
                <?php echo $form->labelEx($model, 'message', ['class' => 'form-label-left']); ?>
                <div class="form-input">
                    <?php echo $form->textArea($model, 'message', ['class' => 'form-textarea', 'cols' => '40', 'rows' => '6']); ?>
                </div>
                <?php echo $form->error($model, 'message'); ?>
            </li>
            <li class="form-line">
                <?php echo $form->labelEx($model, 'email', ['class' => 'form-label-left']); ?>
                <div class="form-input">
                    <?php echo $form->textField($model, 'email', ['placeholder' => Yii::t('site', 'Enter your email'), 'class' => 'form-textbox email-field', 'size' => 30]); ?>
                </div>
                <?php echo $form->error($model, 'email'); ?>
            </li>
            <li class="form-line">
                <div class="form-input-wide">
                    <div class="form-buttons-wrapper">
                        <?php echo CHtml::submitButton(Yii::t('site', 'Send'), ['class' => 'form-submit-button', 'name' => 'submit']); ?>
                    </div>
                </div>
            </li>
        </ul>
    </div>

    <?php $this->endWidget(); ?>
</div>