<section class="partial">
    <label class="partial-label"><?= __FILE__ ?></label>

    <?php
    $scoreName = ($user->profile->assessment_results_render_type == "standard")
        ? "stars-selected" : "percentile-selected";

    $scoreRender = function(Invite $invite) {
        return $this->renderPartial('//global_partials/_simulation_stars', [
            'simulation'     => $invite->simulation,
            'isDisplayTitle' => false,
            'isDisplayArrow' => false,
            'isDisplayScaleIfSimulationNull' => false,
        ],false);
    };

    $this->widget('zii.widgets.grid.CGridView', [
        'dataProvider' => Invite::model()->searchCorporateInvites(
            Yii::app()->user->data()->id
        ),
        'summaryText' => '',
        'emptyText'   => '',
        'nullDisplay' => '',
        'htmlOptions' => [
            'class' => 'contrast-table locator-contrast-table'
        ],
        'pager' => [
            'header'         => false,
            'firstPageLabel' => '',
            'prevPageLabel'  => 'Назад',
            'nextPageLabel'  => 'Вперед',
            'lastPageLabel'  => '',
        ],
        'columns' => [
            ['header' => ''                           , 'value' => '', 'type' => 'html'],
            ['header' => Yii::t('site', 'Full name')  , 'name' => 'name'        , 'value' => '$data->firstname." ".$data->lastname'],
            ['header' => Yii::t('site', 'Position')   , 'name' => 'vacancy_id'  , 'value' => '(Yii::t("site", $data->getVacancyLabel()) !== null) ? Yii::t("site", $data->getVacancyLabel()) : "-"', 'type' => 'raw'],
            [
                'header' => Yii::t('site', 'Status'),
                'name' => 'status',
                'value' => function(Invite $data){
                    return '
                        <span class="action-display-popover inter-active table-link color-428290">'
                        . Yii::t("site", $data->getStatusText())
                        . '<div class="hide inner-popover background-sky">
                            <div class="popover-triangle locator-popover-triangle-left"></div>
                            <div class="popover-wrapper">
                                <div class="popover-content">'
                                   . $data->getStatusDescription()
                                . '</div>
                              </div>
                          </div> </span>';
                    },
                'type' => 'raw'
            ],
            [
                'header' => Yii::t('site', 'Date'),
                'name'   => 'sent_time'   ,
                'value'  => '$data->getDateForDashboard()',
                'type'   => 'raw'
            ],

            ['header' => 'Рейтинг <span class="
                action-switch-assessment-results-render-type
                action-display-assessment-results-type-hint
                locator-assessment-results-type-switcher
                assessment-results-type-switcher inter-active '.$scoreName.'"
                ></span>', 'value' => $scoreRender, 'type' => 'raw'],
            ['header' => '', 'value' => '"<span class=\"inviteaction action-delete-invite\" data-status=\"$data->status\" data-href=\"/dashboard/invite/remove/$data->id\">Удалить</span>"', 'type' => 'raw', 'htmlOptions' => ['class' => 'hide']],
            ['header' => '', 'value' => '"<a class=\"inviteaction\" href=\"/dashboard/invite/resend/$data->id\">Отправить ещё раз</a>"' , 'type' => 'html', 'htmlOptions' => ['class' => 'hide']],
        ]
    ]);
    ?>
</section>


<div class="hint-assessment-results-type-switcher inner-popover background-yellow hide
    locator-hint-assessment-results-type-switcher us-hint-assessment-type">
    <div class="popover-triangle-upper"></div>
    <div class="popover-wrapper">
        <div class="popover-content">
            Переключение между относительным и абсолютным рейтингом
        </div>
    </div>
</div>