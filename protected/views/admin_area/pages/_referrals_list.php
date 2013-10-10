<?php

$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => $dataProvider,
    'summaryText' => '',
    'emptyText'   => '',
    'ajaxUpdate'=>false,
    'nullDisplay' => '',
    'filter'      => new UserReferral(),
    'pager' => [
        'header'         => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel'  => 'Назад',
        'nextPageLabel'  => 'Вперед',
        'lastPageLabel'  => 'конец >>',
    ],
    'columns' => [
        ['header' => 'Пользователь'            , 'name' => 'referrer_id',
         'value'  => function($data) {
                                        $referrer = YumUser::model()->findByPk($data->referrer_id);
                                        if($referrer !== null) {
                                            return '<a href="/admin_area/user/'.$referrer->profile->user_id
                                            .'/details" target="_black" >' .
                                            $referrer->profile->firstname . " " . $referrer->profile->lastname .
                                            '</a>';
                                        }
                                    },
         'type'  => 'raw',
        ],

        ['header' => 'Реферал',
            'name'   => 'referral_id',
            'value'  => function($data) {
                if($data->referral_id !== null) {
                    $referral = YumUser::model()->findByPk($data->referral_id);
                    return '<a href="/admin_area/user/'.$referral->getAccount()->user_id
                    .'/details" target="_black" >' .
                    $referral->profile->firstname . " " . $referral->profile->lastname .
                    '</a>';
                }
                else {
                    return "";
                }
            },
            'type'  => "raw"
        ],
        ['header' => 'Дата приглашения'  , 'name' => 'invited_at'    , 'value' => '$data->invited_at',
        'filter'  => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'model'=>new UserReferral(),
                    'attribute'=>'invited_at',
                    'language'=>'ru',
                    'options'=>array(
                        'showAnim'=>'fold',
                        'dateFormat'=>'yy-mm-dd',
                        'changeMonth' => 'true',
                        'changeYear'=>'true',
                    ),
                ),true)
        ],

        ['header' => 'Дата регистрации' , 'name' => 'registered_at' , 'value' => '($data->registered_at === null) ? "Не зарегистрирован" : "$data->registered_at"',
        'filter'  => $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                     'model'=>new UserReferral(),
                     'attribute'=>'registered_at',
                     'language'=>'ru',
                     'options'=>array(
                         'showAnim'=>'fold',
                         'dateFormat'=>'yy-mm-dd',
                         'changeMonth' => 'true',
                         'changeYear'=>'true',
                     ),
                     ),true),
        ],
        ['header' => 'Причина отказа'   , 'name' => 'reject_reason', 'value' => '$data->reject_reason', 'filter' => false],
        ['header' => 'Статус'           , 'name' => 'status',
         'value'  => function($data) {
                         switch($data->status) {
                             case "pending" :
                                 return "В ожидании";
                                 break;

                             case "approved" :
                                 return "Одобрен";
                                 break;

                             case "rejected" :
                                 return "Отклонен";
                                 break;
                         }
                     },
          'filter' => '<select name="UserReferral[status]">
                            <option></option>
                            <option value="pending">В ожиданиие</option>
                            <option value="approved">Одобрен</option>
                            <option value="rejected">Отклонен</option>
                       </select>'
         ],

    ]
]);
?>