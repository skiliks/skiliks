<?

    $this->widget('zii.widgets.grid.CGridView', [
        'dataProvider' => Referrer::model()->searchUserReferrals(
        Yii::app()->user->data()->id
    ),
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
            ['header' => 'E-mail'            , 'name' => 'referrer_email', 'value' => '$data->referrer_email'],
            ['header' => 'Приглашен'         , 'name' => 'invited_at'    , 'value' => '$data->invited_at'    ],
            ['header' => 'Зарегистрировался' , 'name' => 'registered_at' , 'value' => '($data->registered_at === null) ? "Не зарегистрирован" : "Зарегистрирован"' ],
        ]
    ]);
?>