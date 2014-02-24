<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => $dataProvider,
    'summaryText' => '',
    'emptyText'   => '',
    'ajaxUpdate'=>false,
    'nullDisplay' => '',
    'filter'      => new SiteLogAuthorization(),
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
        ['header' => 'Попытка', 'name' => 'is_success', 'value' => '$data->getStatus()'],
        ['header' => 'User Agent', 'name' => 'user_agent', 'value' => '$data->user_agent'],
        ['header' => 'Дата', 'name' => 'date', 'value' => '$data->date'],
        ['header' => 'Логин', 'name' => 'login', 'value' => '$data->login'],
        ['header' => 'Пароль', 'name' => 'password', 'value' => '$data->password'],
        ['header' => 'User Id', 'name' => 'user_id', 'value' => '$data->user_id'],
        ['header' => 'Тип входа', 'name' => 'type_auth', 'value' => '$data->type_auth'],
        ['header' => 'Referral url', 'name' => 'referer_url', 'value' => '$data->referer_url'],

    ]
]);