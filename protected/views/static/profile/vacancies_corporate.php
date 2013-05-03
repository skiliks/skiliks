
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">
    <?php $this->renderPartial('_menu_corporate', ['active' => ['vacancies' => true]]) ?>

    <?php // LIST: ?>
    <div class="profileform radiusthree">
        <div class="vacancy-list">
        <?php
        $this->widget('zii.widgets.grid.CGridView', [
            'dataProvider' => Vacancy::model()->search(Yii::app()->user->data()->id),
            'summaryText' => '',
            'emptyText' => '',
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
                ['header' => '', 'name' => '' , 'value' => '($row+1)."."'],
                ['header' => Yii::t('site', 'Name'), 'name' => 'label' , 'value' => '$data->label'],
                ['header' => Yii::t('site', 'Link'), 'name' => 'link'  , 'value' => '$data->link'],
                ['header' => ''                                        , 'value' => '"<a class=\"edit-vacancy-link\" href=\"/profile/corporate/vacancy/$data->id/edit\">редактировать</a>"'                   , 'type' => 'html'],
                ['header' => ''                                        , 'value' => '"<a class=\"delete-vacancy-link\" href=\"/profile/corporate/vacancy/$data->id/remove\">удалить</a>"' , 'type' => 'html'],
            ]
        ]);
        ?>
        </div>
    <?PHP // ADD FORM: ?>

    <?php $isDisplayForm = (null === $vacancy->id && 0 == count($vacancy->getErrors())) ?>

    <a class="vacancy-add-form-switcher" style="<?php echo ($isDisplayForm) ? '' : 'display: none;'?> ;" >Добавить</a>

    <?php // add_vacancy_form { ?>
        <div class="form form-vacancy" style="<?php echo ($isDisplayForm) ? 'display: none;' : ''?> ;">
            <?php $this->renderPartial('//global_partials/_add_vacancy_form', [
                'vacancy'         => $vacancy,
                'positionLevels'  => $positionLevels,
                'specializations' => $specializations,
            ]) ?>
        </div>
    <?php // add_vacancy_form } ?>

</div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $(".vacancy-add-form-switcher").click(function(event) {
            event.preventDefault();
            $(".form-vacancy").show();
            $(".vacancy-add-form-switcher").hide();
        });
    });
</script>