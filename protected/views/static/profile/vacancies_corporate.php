
<h1><?php echo Yii::t('site', 'Profile') ?></h1>

<?php $this->renderPartial('_menu_corporate', ['active' => ['vacancy' => true]]) ?>

<?php // LIST: ?>

<h2>Вакансии</h2>

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
        ['header' => ''                                        , 'value' => '"<a href=\"/profile/corporate/vacancy/$data->id/edit\">редактировать</a>"'                   , 'type' => 'html'],
        ['header' => ''                                        , 'value' => '"<a class=\"delete-vacancy-link\" href=\"/profile/corporate/vacancy/$data->id/remove\">удалить</a>"' , 'type' => 'html'],
    ]
]);
?>

<br/>
<br/>

<hr/>

<?PHP // ADD FORM: ?>

<?php $isDisplayForm = (null === $vacancy->id && 0 == count($vacancy->getErrors())) ?>

<a class="vacancy-add-form-switcher" style="<?php echo ($isDisplayForm) ? '' : 'display: none;'?> ;" >Добавить</a>

<?php if (null !== $vacancy->id): ?>
    <a href="/profile/corporate/vacancies/">Вернутсья к форме добавления вакансий</a>
    <br/>
    <br/>
    <h2>Редактирование вакансии "<?php echo $vacancy->label ?>"</h2>
<?php endif ?>

<div class="form form-vacancy" style="<?php echo ($isDisplayForm) ? 'display: none;' : ''?> ;">

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
                'Выберите род деятельности'
            ),
            [
                'ajax' => [
                    'type'     => 'POST',
                    'dataType' =>'json',
                    'url'      => $this->createUrl('profile/getSpecialization'),
                    'success'  =>' function(data) {
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
        <?php echo $form->labelEx($vacancy  , 'label'); ?>
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
        <?php echo CHtml::submitButton('Сохранить изменения', ['name' => 'add']); ?>
    </div>

    <br/>
    <br/>

    * Поля обязательные для заполнения

    <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
    $(function(){
        $(".vacancy-add-form-switcher").click(function(event) {
            event.preventDefault();
            $(".form-vacancy").show();
            $(".vacancy-add-form-switcher").hide();
        });

        $('a.delete-vacancy-link').click(function(event) {
            if (confirm("Вы желаете удалить вакансию \"" + $(this).parent().parent().find('td:eq(0)').text() + "\"?")) {
                // link go ahead to delete URL
            } else {
                event.preventDefault();
            }

        });
    })
</script>