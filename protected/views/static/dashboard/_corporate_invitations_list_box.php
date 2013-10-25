<?php
$scoreName = ($user->profile->assessment_results_render_type == "standard") ? "percentile-toggle-off" : "percentile-toggle-on";

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
    'emptyText' => '',
    'nullDisplay' => '',
    'pager' => [
        'header'        => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel' => 'Назад',
        'nextPageLabel' => 'Вперед',
        'lastPageLabel' => 'конец >>',
    ],
    'columns' => [
        ['header' => ''                           , 'value' => '', 'type' => 'html'],
        ['header' => Yii::t('site', 'Full name')  , 'name' => 'name'        , 'value' => '$data->firstname." ".$data->lastname'],
        ['header' => Yii::t('site', 'Position')   , 'name' => 'vacancy_id'  , 'value' => '(Yii::t("site", $data->getVacancyLabel()) !== null) ? Yii::t("site", $data->getVacancyLabel()) : "-"', 'type' => 'raw'],
        ['header' => Yii::t('site', 'Status')     , 'name' => 'status'      , 'value' => 'Yii::t("site", $data->getStatusText())'],
        [
            'header' => Yii::t('site', 'Date'),
            'name' => 'sent_time'   ,
            'value' => function (Invite $data) { return $data->getUpdatedTime()->format("j/m/y");},
            'type' => 'raw'
        ],

        ['header' => 'Рейтинг <span class="change-simulation-result-render percentile-hover-toggle-span '.$scoreName.'"></span>', 'value' => $scoreRender, 'type' => 'raw'],
        ['header' => '', 'value' => '"<a class=\"inviteaction\" href=\"/dashboard/invite/remove/$data->id\">Удалить</a>"', 'type' => 'html'],
        ['header' => '', 'value' => '"<a class=\"inviteaction\" href=\"/dashboard/invite/resend/$data->id\">Отправить ещё раз</a>"' , 'type' => 'html'],
    ]
]);
?>

<?

?>
<?php // edit invite pop-up form { ?>
    <?php //PHP: ?>
        <?php $this->renderPartial('_edit-invite-pop-up-form', [
            'invite'    => $inviteToEdit,
            'vacancies' => $vacancies,
        ]) ?>

    <?php // java-script: ?>
<?php // edit invite pop-up form } ?>
<div class="popover popover-div-on-hover"><div class="popover-triangle"></div><div class="popover-content"><div class="popup-content ProximaNova">Переключение между относительным и абсолютным рейтингом.</div></div></div>
