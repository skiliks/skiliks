<?php $assetsUrl = $this->getAssetsUrl(); ?>
<style>
    /* надо переместить в файл со стилями :) */
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

<h1 class="page-header"><?php echo Yii::t('site', 'Profile') ?></h1>
<div class="container-3 block-border border-primary bg-transparnt">

    <div class="border-primary bg-yellow standard-left-box"><?php $this->renderPartial('//new/_menu_corporate', ['active' => ['personal-data' => true]]) ?></div>

    <div class="border-primary bg-light-blue standard-right-box">
        <div class="pad-large profileform accnt-corprt-form profilelabel-wrap profile-min-height">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'account-corporate-personal-form'
            )); ?>

            <div class="row row-inputs">
                <?php echo $form->labelEx($profile, 'Имя'); ?>
                <?php echo $form->textField($profile, 'firstname', ['id' => 'profile_firstname']); ?><?php echo $form->error($profile, 'firstname'); ?><?php echo $form->textField($profile, 'lastname', ['id' => 'profile_lastname']); ?><?php echo $form->error($profile, 'lastname'); ?>
            </div>

            <div class="row">
                <?php echo $form->labelEx($profile  , 'email'); ?>
                <strong class="font-large"><?php echo $profile->email; ?></strong>
            </div>

            <div class="row row-selects">
                <?php echo $form->labelEx($account     , 'Должность'); ?>
                <?php echo $form->dropDownList($account, 'position_id', $positions); ?><?php echo $form->error($account       , 'position_id'); ?>
            </div>

            <div class="row buttons">
                <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), ['name' => 'save', 'class' => 'btn btn-large btn-green']); ?>
            </div>

            <?php $this->endWidget(); ?>
        </div>
    </div>
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
<script>
    $(document).ready(function(){
        var errors = $(".errorMessage");
        for (var i=0; i < errors.length;i++) {
            var inp = $(errors[i]).prev("input.error");
            $(inp).css({"border":"2px solid #bd2929"});
            $(errors[i]).addClass($(inp).attr("id"));
        }
    });
</script>