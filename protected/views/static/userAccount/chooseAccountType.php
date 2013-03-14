<style>
    .form label {
        display: inline-block;
        width: 160px;
    }
    .form .errorMessage {
        background-color: #f5dfb4;
        border: 1px solid #cd0a0a;
        border-radius: 7px;
        color: #cd0a0a;
        margin: 5px;
        opacity: 0.7;
        padding: 7px;
        width: 340px;
    }
</style>


<br/>
<br/>
<br/>

<div class="form form-account-personal" style="width: 450px; float: left;">

    <h1><?php echo Yii::t('site', 'Personal account') ?></h1>
    <br/>

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'                   => 'user-account-personal-form',
        'enableAjaxValidation' => false,
    )); ?>

    <?php // echo $form->errorSummary($accountPersonal); ?>

    <div class="row">
        <?php echo $form->hiddenField($accountPersonal, 'user_id'); ?>
        <?php echo $form->error($accountPersonal      , 'user_id'); ?>

        <?php echo $form->hiddenField($profile, 'email'); ?>
        <?php echo $form->error($profile      , 'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($profile  , 'firstname'); ?>
        <?php echo $form->textField($profile, 'firstname'); ?>
        <?php if ($isPersonalSubmitted): ?>
            <?php echo $form->error($profile    , 'firstname'); ?>
        <?php endif; ?>
    </div>
    <br/>

    <div class="row">
        <?php echo $form->labelEx($profile  , 'lastname'); ?>
        <?php echo $form->textField($profile, 'lastname'); ?>
        <?php if ($isPersonalSubmitted): ?>
            <?php echo $form->error($profile    , 'lastname'); ?>
        <?php endif; ?>
    </div>
    <br/>

    <div class="row">
        <?php echo $form->labelEx($accountPersonal     ,'industry_id'); ?>
        <?php echo $form->dropDownList($accountPersonal,'industry_id', $industries); ?>
        <?php echo $form->error($accountPersonal       ,'industry_id'); ?>
    </div>
    <br/>

    <div class="row">
        <?php echo $form->labelEx($accountPersonal     ,'position_id'); ?>
        <?php echo $form->dropDownList($accountPersonal,'position_id', $positions); ?>
        <?php echo $form->error($accountPersonal       ,'position_id'); ?>
    </div>
    <br/>

    <div class="row buttons">
        <?php echo CHtml::submitButton(Yii::t('site', 'Submit'), ['name' => 'personal']); ?>
    </div>
    <br/>

    <?php $this->endWidget(); ?>

</div>

<!-- --------------------------------------------------------------------------------------------------------- -->

<div class="form form-account-corporate" style="width: 450px; float: right;">

    <h1><?php echo Yii::t('site', 'Corporate account') ?></h1>
    <br/>

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id'                   => 'user-account-corporate-form',
        'enableAjaxValidation' => false,
    )); ?>

    <?php // echo $form->errorSummary($accountCorporate); ?>

    <div class="row">
        <?php echo $form->hiddenField($accountCorporate,'user_id'); ?>
        <?php echo $form->error($accountCorporate,'user_id'); ?>

        <?php echo $form->hiddenField($profile,'email'); ?>
        <?php echo $form->error($profile      , 'email'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($profile  , 'firstname'); ?>
        <?php echo $form->textField($profile, 'firstname'); ?>
        <?php if ($isCorporateSubmitted): ?>
            <?php echo $form->error($profile    , 'firstname'); ?>
        <?php endif; ?>
    </div>
    <br/>

    <div class="row">
        <?php echo $form->labelEx($profile  , 'lastname'); ?>
        <?php echo $form->textField($profile, 'lastname'); ?>
        <?php if ($isCorporateSubmitted): ?>
            <?php echo $form->error($profile    , 'lastname'); ?>
        <?php endif; ?>
    </div>
    <br/>

    <div class="row">
        <?php echo $form->labelEx($accountCorporate     , 'industry_id'); ?>
        <?php echo $form->dropDownList($accountCorporate, 'industry_id', $industries); ?>
        <?php echo $form->error($accountCorporate       , 'industry_id'); ?>
    </div>
    <br/>

    <div class="row">
    <?php if (UserService::isCorporateEmail($profile->email)): ?>
        <?php echo $form->hiddenField($accountCorporate, 'corporate_email'); ?>
    <?php else: ?>
        <?php echo $form->labelEx($accountCorporate  , 'corporate_email'); ?>
        <?php echo $form->textField($accountCorporate, 'corporate_email'); ?>
        <?php echo $form->error($accountCorporate    , 'corporate_email'); ?>
    <?php endif ?>
    </div>
    <br/>

    <div class="row buttons">
        <?php echo CHtml::submitButton(Yii::t('site', 'Submit'), ['name' => 'corporate']); ?>
    </div>
    <br/>

    <?php $this->endWidget(); ?>

</div>

<!-- --------------------------------------------------------------------------------------------------------- -->

<p class="note" style="float: none; clear: both;">
    <?php echo Yii::t('site', 'Fields with * are required.') ?>
</p>