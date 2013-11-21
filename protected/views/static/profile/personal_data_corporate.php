<?php $assetsUrl = $this->getAssetsUrl(); ?>
<style>
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

<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>
<a href="/profile/save-analytic-file-2?version=v1">Аналитический файл v1</a>
<a href="/profile/save-analytic-file-2?version=v2">Аналитический файл v2</a>
<div class="transparent-boder profilewrap">

<?php $this->renderPartial('_menu_corporate', ['active' => ['personal-data' => true]]) ?>

    <div class="form profileform radiusthree">

        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'account-corporate-personal-form'
        )); ?>

        <div class="row">
            <?php echo $form->labelEx($profile, 'Имя'); ?>
            <?php echo $form->textField($profile, 'firstname', ['id' => 'profile_firstname']); ?><?php echo $form->error($profile, 'firstname'); ?>
            <?php echo $form->textField($profile, 'lastname', ['id' => 'profile_lastname']); ?><?php echo $form->error($profile, 'lastname'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($profile, 'email'); ?>
            <span class="value"><?php echo $profile->email; ?></span>
        </div>

        <div class="row cposwrap">
            <?php echo $form->labelEx($account     , 'Должность'); ?>
            <?php echo $form->dropDownList($account, 'position_id', $positions); ?><?php echo $form->error($account       , 'position_id'); ?>
        </div>

        <div class="row buttons">
            <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), ['name' => 'save']); ?>
        </div>

        <?php $this->endWidget(); ?>
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