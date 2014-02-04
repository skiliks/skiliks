
<section class="page-title-box column-full pull-content-left ">
    <h1 class="margin-bottom-standard"><?php echo Yii::t('site', 'Profile') ?></h1>
</section>

<section class="pull-content-left nice-border reset-padding  overflow-hidden
    border-radius-standard background-transparent-20
    locator-content-box">

    <!--div class="transparent-boder profilewrap"-->
    <aside class="column-1-3 inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_corporate', ['active' => ['vacancies' => true]]) ?>
    </aside>

    <section class="column-2-3-wide inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side">

        <div class="vacancy-list margin-bottom-standard">
            <?php
            $this->widget('zii.widgets.grid.CGridView', [
                'dataProvider' => Vacancy::model()->search(Yii::app()->user->data()->id),
                'summaryText' => '',
                'emptyText' => '',
                'hideHeader'    => true,
                'htmlOptions' => [
                    'class' => 'light-list-table locator-light-list-table'
                ],
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
                    ['header' => ''                                        , 'value' => '"<a class=\"icon-16 icon-edit\" href=\"/profile/corporate/vacancy/$data->id/edit\" title=\"редактировать\"></a>"' , 'type' => 'html'],
                    ['header' => ''                                        , 'value' => '"<a class=\"icon-16 icon-delete\" href=\"/profile/corporate/vacancy/$data->id/remove\" title=\"удалить\"></a>"' , 'type' => 'html'],
                ]
            ]);
            ?>
        </div>

        <?php // add vacancy form { ?>

        <?php $isHideForm = (null === $vacancy->id && 0 == count($vacancy->getErrors())) ?>

        <!-- vacancy-add-form-switcher -->
        <span class="action-vacancy-add-form-display action-toggle-hide
            button-white inter-active label icon-arrow-blue reset-margin
            action-show-add-vacancy-form
            <?php echo ($isHideForm) ? '' : 'hide' ?>">
            Добавить
        </span>

        <div class="locator-add-vacancy-form <?php echo ($isHideForm) ? 'hide' : ''; ?>">
            <?php $this->renderPartial('//global_partials/_add_vacancy_form', [
                'vacancy'         => $vacancy,
                'positionLevels'  => $positionLevels,
                'specializations' => $specializations,
            ]) ?>
        </div>

        <?php // add_vacancy_form } ?>

    </section>
</section>

<div class="clearfix column-full"></div>