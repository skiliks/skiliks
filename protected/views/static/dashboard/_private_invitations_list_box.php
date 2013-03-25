<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => Invite::model()->searchByInvitedUserEmail(Yii::app()->user->data()->profile->email), //$dataProvider,
    'summaryText' => '',
    'pager' => [
        'header'        => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel' => '< назад',
        'nextPageLabel' => 'далее >',
        'lastPageLabel' => 'конец >>',
    ],
    'columns' => [
        ['header' => Yii::t('site', 'Position')   , 'name' => 'position_id' , 'value' => 'Yii::t("site", $data->vacancy->label)'],
        ['header' => Yii::t('site', 'Status')     , 'name' => 'status'      , 'value' => 'Yii::t("site", $data->getStatusText())'],
        ['header' => Yii::t('site', 'Date / time'), 'name' => 'sent_time'   , 'value' => '$data->getSentTime()->format("j/m/y G\h i\m")'],
        ['header' => Yii::t('site', 'Score')                                , 'value' => '"-"'],
        ['header' => ''                                                     , 'value' => '"<a href=\"/$data->id\">принять</a>"' , 'type' => 'html'],
        ['header' => ''                                                     , 'value' => '"<a href=\"/$data->id\">отказать</a>"', 'type' => 'html'],
    ]
]);
?>