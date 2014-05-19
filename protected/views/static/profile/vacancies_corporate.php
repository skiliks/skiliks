
<section class="page-title-box column-full pull-content-center">
    <h1 class="margin-bottom-standard"><?php echo Yii::t('site', 'Profile') ?></h1>
</section>

<section class="pull-content-left nice-border reset-padding  overflow-hidden us-profile-width font-always-14px
    shadow-14 border-radius-standard background-transparent-20 pull-center
    locator-content-box">

    <!--div class="transparent-boder profilewrap"-->
    <aside class="inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_corporate', ['active' => ['vacancies' => true]]) ?>
    </aside>

    <section class="inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side locator-profile-right-side"
             style="padding-top: 20px;">

        <?php $vacancyCounter = Vacancy::model()->count('user_id = :user_id', [
            'user_id' => Yii::app()->user->data()->id
        ]);?>

        <div class="vacancy-list" style="<?= (0 == $vacancyCounter) ? 'display: none;' : '' ?>">
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
                    ['header' => '', 'name' => '' , 'value' => '(10*(Yii::app()->request->getParam("page",1) - 1) + $row + 1)."."'],
                    ['header' => Yii::t('site', 'Name'), 'name' => 'label' , 'value' => '$data->label'],
                    ['header' => Yii::t('site', 'Link'), 'name' => 'link'  , 'value' => '$data->getCroppedUrl()' , 'type' => 'html'],
                    [
                        'header'      => '',
                        'value'       => '"<a class=\"icon-16 icon-edit\" href=\"/profile/corporate/vacancy/$data->id/edit\" title=\"редактировать\"></a>"',
                        'type'        => 'html',
                        'htmlOptions' => ['width' => '20px']
                    ],
                    [
                        'header'      => '',
                        'value'       => '"<a class=\"icon-16 icon-delete\" href=\"/profile/corporate/vacancy/$data->id/remove\" title=\"удалить\"></a>"',
                        'type'        => 'html',
                        'htmlOptions' => ['width' => '20px'],
                    ],
                ]
            ]);
            ?>
        </div>

        <?php // add vacancy form { ?>

        <?php $isHideForm = (null === $vacancy->id && 0 == count($vacancy->getErrors())) ?>

        <!-- vacancy-add-form-switcher -->
        <span class="action-vacancy-add-form-display action-toggle-hide
            button-white button-white-hover inter-active label icon-arrow-blue reset-margin
            action-show-add-vacancy-form
            <?php echo ($isHideForm) ? '' : 'hide' ?>" >
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