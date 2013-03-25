
<h1><?php echo Yii::t('site', 'Profile') ?></h1>

<?php $this->renderPartial('_menu_corporate', ['active' => ['vacancy' => true]]) ?>

<?php // LIST: ?>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => Vacancy::model()->search(Yii::app()->user->data()->id), //$dataProvider,
    'summaryText' => '',
    'hideHeader'    => true,
    'pager' => [
        'header'        => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel' => '< назад',
        'nextPageLabel' => 'далее >',
        'lastPageLabel' => 'конец >>',
        'pageSize'      => 5
    ],
    'columns' => [
        ['header' => Yii::t('site', 'Name'), 'name' => 'label' , 'value' => '$data->label'],
        ['header' => Yii::t('site', 'Link'), 'name' => 'link'  , 'value' => '$data->link'],
        ['header' => ''                                        , 'value' => '"<a class=\"edit-invite\">исправить</a>"', 'type' => 'html'],
        ['header' => ''                                        , 'value' => '"<a href=\"/dashboard/invite/remove/$data->id\">удалить</a>"'                , 'type' => 'html'],
    ]
]);
?>

<br/>
<br/>

<?PHP // ADD FORM: ?>

<div class="form form-vacancy">

    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'add-vacancy-form',
    )); ?>

    <div class="row">
        <?php echo $form->labelEx($vacancy     , 'professional_occupation_id'); ?>
        <?php echo $form->dropDownList(
            $vacancy,
            'professional_occupation_id',
            StaticSiteTools::formatValuesArrayLite(
                'ProfessionalOccupation',
                'id',
                'label',
                '',
                'Выбирите род деятельности'
            ),
            [
                'ajax' => [
                    'type'     => 'POST',
                    'dataType' =>'json',
                    'url'      => $this->createUrl('profile/getSpecialization'),
                    'success'  =>' function(data) {
                        console.log(data);
                        $("select#Vacancy_professional_specialization_id option").remove();
                        for (var id in data) {
                            $("select#Vacancy_professional_specialization_id").append(
                                "<option value=\"" + id + "\">" + data[id] + "</option>"
                            );
                        }

                        // refresh custom drop-down
                        $("select#Vacancy_professional_specialization_id").selectbox("detach");
                        $("select#Vacancy_professional_specialization_id").selectbox("attach");
                    }',
                ],
            ]
        ); ?>
        <?php echo $form->error($vacancy       , 'professional_occupation_id'); ?>
    </div>

    <br/>
    <br/>

    <div class="row">
        <?php echo $form->labelEx($vacancy     , 'professional_specialization_id'); ?>
        <?php echo $form->dropDownList($vacancy, 'professional_specialization_id', $specializations); ?>
        <?php echo $form->error($vacancy       , 'professional_specialization_id'); ?>
    </div>

    <br/>
    <br/>

    <div class="row">
        <?php echo $form->labelEx($vacancy , 'label'); ?>
        <?php echo $form->textField($vacancy, 'label'); ?>
        <?php echo $form->error($vacancy    , 'label'); ?>
    </div>

    <br/>
    <br/>

    <div class="row">
        <?php echo $form->labelEx($vacancy  , 'link'); ?>
        <?php echo $form->textField($vacancy, 'link'); ?>
        <?php echo $form->error($vacancy    , 'link'); ?>
    </div>

    <br/>
    <br/>

    <div class="row buttons">
        <?php echo CHtml::submitButton('Сохранить', ['name' => 'add']); ?>
    </div>

    <br/>
    <br/>

    * Поля обязательные для заполнения

    <?php $this->endWidget(); ?>
</div>