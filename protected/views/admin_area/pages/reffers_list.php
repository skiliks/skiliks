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

            ['header' => 'Позьователь',
             'name'   => 'user_name',
             'value'  => function($data) {
                                             if($data->referrer_id !== null) {
                                                $refer = YumUser::model()->findByPk($data->referrer_id);
                                                return '<a href="/admin_area/user/'.$refer->account_corporate->user_id
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