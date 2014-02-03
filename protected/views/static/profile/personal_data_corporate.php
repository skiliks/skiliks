<style>
    <?php /*
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
 */ ?>
</style>

<section class="page-title-box column-full pull-content-left ">
    <h1 class="bottom-margin-standard"><?php echo Yii::t('site', 'Profile') ?></h1>
</section>

<section class="pull-content-left nice-border reset-padding
    border-radius-standard background-transparent-20">

    <!--div class="transparent-boder profilewrap"-->
    <aside class="column-1-3 inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_corporate', ['active' => ['personal-data' => true]]) ?>
    </aside>

    <section class="column-2-3-wide inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side">
        <div class="">

            <?php $form = $this->beginWidget('CActiveForm', array(
                'id' => 'account-corporate-personal-form'
            )); ?>

            <div class="row <?= $this->hasErrors($form, $profile, 'firstname') ?> <?= $this->hasErrors($form, $profile, 'lastname') ?>">
                <span class="error-place">
                    <?php // нет смысла делать клпсс такому не стандартному элементу ?>
                    <span class="unstandard-error-firstname">
                        <?php echo $form->error($profile, 'firstname'); ?>
                    </span>
                    <span class="unstandard-error-firstname">
                        <?php echo $form->error($profile, 'lastname'); ?>
                    </span>
                </span>
                <?php echo $form->labelEx($profile, 'Имя'); ?>
                <?php echo $form->textField($profile, 'firstname', ['id' => 'profile_firstname']); ?>
                <?php echo $form->textField($profile, 'lastname', ['id' => 'profile_lastname']); ?>
            </div>

            <div class="row">
                <?php echo $form->labelEx($profile, 'email'); ?>
                <span class="value"><?php echo $profile->email; ?></span>
            </div>

            <div class="row cposwrap">
                <?php echo $form->labelEx($account , 'Должность'); ?>
                <span class="error-place">
                    <?php echo $form->error($account , 'position_id'); ?>
                </span>
                <?php echo $form->dropDownList($account, 'position_id', $positions); ?>
            </div>

            <div class="row buttons">
                <?php echo CHtml::submitButton(Yii::t('site', 'Сохранить изменения'), [
                    'name'  => 'save',
                    'class' => 'label background-dark-blue icon-circle-with-blue-arrow-big button-standard icon-padding-standard',
                ]); ?>
            </div>

            <?php $this->endWidget(); ?>
        </div>
    </section>
</section>

    <script>
//        $(document).ready(function(){
//            var errors = $(".errorMessage");
//            for (var i=0; i < errors.length;i++) {
//                var inp = $(errors[i]).prev("input.error");
//                $(inp).css({"border":"2px solid #bd2929"});
//                $(errors[i]).addClass($(inp).attr("id"));
//            }
//        });
    </script>