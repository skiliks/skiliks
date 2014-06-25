<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => $dataProvider,
    'summaryText'  => '',
    'emptyText'    => '',
    'ajaxUpdate'   => false,
    'nullDisplay'  => '',
    'filter'       => new SiteLogPermissionChanges(),
    'pager' => [
        'header'         => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel'  => 'Назад',
        'nextPageLabel'  => 'Вперед',
        'lastPageLabel'  => 'конец >>',
        'pageSize' => 10,
    ],
    'columns' => [
        ['header' => 'Инициатор', 'value' => '$data->Initiator->profile->email'],
        ['header' => 'Дата правки', 'name' => 'created_at', 'value' => '$data->created_at'],
        ['header' => 'Лог правки', 'name' => 'result', 'value' => '$data->result', 'type' => 'raw'],
    ]
]);