
<h1 class="page-header"><?php echo Yii::t('site', 'Profile') ?></h1>

<div class="container-3 block-border border-primary bg-transparnt">
    <div class="border-primary bg-yellow standard-left-box"><?php $this->renderPartial('//new/_menu_corporate', ['active' => ['vacancies' => true]]) ?></div>

    <div class="border-primary bg-light-blue standard-right-box">
        <div class="pad-large profilelabel-wrap profile-min-height">
            <?php // LIST: ?>
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
                        ['header' => Yii::t('site', 'Link'), 'name' => 'link'  , 'value' => '$data->getCroppedUrl()'],
                        ['header' => ''                                        , 'value' => '"<a class=\"edit-vacancy-link\" href=\"/profile/corporate/vacancy/$data->id/edit\">редактировать</a>"'                   , 'type' => 'html'],
                        ['header' => ''                                        , 'value' => '"<a class=\"delete-vacancy-link\" href=\"/profile/corporate/vacancy/$data->id/remove\">удалить</a>"' , 'type' => 'html'],
                    ]
                ]);
                ?>
                </div>
            <?PHP // ADD FORM: ?>

            <?php $isDisplayForm = (null === $vacancy->id && 0 == count($vacancy->getErrors())) ?>

            <a class="vacancy-add-form-switcher btn btn-primary" style="<?php echo ($isDisplayForm) ? '' : 'display: none;'?> ;" >Добавить</a>

            <?php // add_vacancy_form { ?>
                <div class="form-vacancy profileform profilelabel-wrap" style="<?php echo ($isDisplayForm) ? 'display: none;' : ''?> ;">
                    <?php $this->renderPartial('//global_partials/_add_vacancy_form', [
                        'vacancy'         => $vacancy,
                        'positionLevels'  => $positionLevels,
                        'specializations' => $specializations,
                    ]) ?>
                </div>
            <?php // add_vacancy_form } ?>
        </div>
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