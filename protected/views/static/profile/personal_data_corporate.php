<?php $assetsUrl = $this->getAssetsUrl(); ?>
<style>
    .form {
        margin: 20px 0 0 30px;
        float: left;
        width: 700px;
    }
    .row {
        clear: both;
        margin: 10px 0;
    }
    label {
        float: left;
        width: 200px;
    }
    .editable {
        cursor: pointer;
    }
    .editable:after {
        content: "";
        display: inline-block;
        width: 13px;
        height: 16px;
        margin-left: 10px;
        background: url(<?= $assetsUrl; ?>/img/icon-pen.png) no-repeat;
    }
</style>

<h1><?php echo Yii::t('site', 'Profile') ?></h1>

<?php $this->renderPartial('_menu_corporate', ['active' => ['personal-data' => true]]) ?>

<div class="form">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'account-corporate-personal-form'
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($profile, 'Name'); ?>
        <?php echo $form->textField($profile, 'firstname', ['id' => 'profile_firstname']); ?>
        <?php echo $form->textField($profile, 'lastname', ['id' => 'profile_lastname']); ?>
        <?php echo $form->error($profile, 'firstname'); ?>
        <?php echo $form->error($profile, 'lastname'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($account  , 'corporate_email'); ?>
        <span class="value"><?php echo $account->corporate_email; ?></span>
    </div>

    <div class="row">
        <?php echo $form->labelEx($account     , 'position_id'); ?>
        <?php echo $form->dropDownList($account, 'position_id', $positions); ?>
        <?php echo $form->error($account       , 'position_id'); ?>
    </div>

    <div class="row buttons">
        <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), ['name' => 'save']); ?>
    </div>

    <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
/*$('.editable').editable(function(value) {
    var names = value.split(/\s+/).slice(0, 2);

    $('#profile_firstname').val(names[0]);
    $('#profile_lastname').val(names[1]);

    return names.join(' ');
}, {
    width: 200,
    onblur: 'submit'
});*/
</script>