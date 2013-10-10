<?php

$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => $dataProvider,
    'summaryText' => '',
    'emptyText'   => '',
    'nullDisplay' => '',
    'pager' => [
        'header'         => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel'  => 'Назад',
        'nextPageLabel'  => 'Вперед',
        'lastPageLabel'  => 'конец >>',
    ],
    'columns' => [
        ['header' => 'E-mail'            , 'name' => 'referral_email', 'value' => '$data->referral_email'],
        ['header' => 'Приглашен'         , 'name' => 'invited_at'    , 'value' => '$data->invited_at'    ],

        ['header' => 'Позьователь',
            'name'   => 'user_name',
            'value'  => function($data) {
                if($data->referral_id !== null) {
                    $refer = YumUser::model()->findByPk($data->referral_id);
                    return '<a href="/admin_area/user/'.$refer->getAccount()->user_id
                    .'/details" target="_black" >' .
                    $refer->profile->firstname . " " . $refer->profile->lastname .
                    '</a>';
                }
                else {
                    return "";
                }
            },
            'type'  => "raw"
        ],

        ['header' => 'Дата регистрации' , 'name' => 'registered_at' , 'value' => '($data->registered_at === null) ? "Не зарегистрирован" : "$data->registered_at"' ],
    ]
]);
?>