<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => $dataProvider,
    'summaryText' => '',
    'emptyText'   => '',
    'ajaxUpdate'=>false,
    'nullDisplay' => '',
    'filter'      => new SiteLogAccountAction(),
    'pager' => [
        'header'         => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel'  => 'Назад',
        'nextPageLabel'  => 'Вперед',
        'lastPageLabel'  => 'конец >>',
    ],
    'columns' => [
        ['header' => 'ID', 'name' => 'id', 'value' => '$data->id'],
        ['header' => 'IP', 'name' => 'ip', 'value' => '$data->ip'],
        ['header' => 'Сообщение', 'name' => 'message', 'value' => '$data->message'],
        ['header' => 'Дата', 'name' => 'date', 'value' => '$data->date'],

    ]
]);