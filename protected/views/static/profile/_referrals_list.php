<?php
    $this->widget('zii.widgets.grid.CGridView', [
        'dataProvider' => $dataProvider,
        'summaryText' => '',
        'emptyText'   => '',
        'nullDisplay' => '',
        'ajaxUpdate'  => false,
        'htmlOptions' => [
            'class' => 'contrast-table',
        ],
        'pager' => [
            'header'         => false,
            'firstPageLabel' => '<< начало',
            'prevPageLabel'  => 'Назад',
            'nextPageLabel'  => 'Вперед',
            'lastPageLabel'  => 'конец >>',
    ],
        'columns' => [
            ['header' => 'E-mail' , 'name' => 'referral_email', 'value' => '$data->referral_email'],
            ['header' => 'Дата'   , 'name' => 'invited_at'    , 'value' => '$data->invited_at'    ],
            ['header' => 'Статус' ,
             'name'   => 'status' ,
             'value' => function($data) {
                 if($data->isApproved()) {
                     $assetsUrl = $this->getAssetsUrl();
                     return '<div style="display:inline-block; margin-top:2px;">1 симуляция</div>' .
                     '<img src="'.$assetsUrl.'/img/referral-bonus.png" style="margin-left: 20px; margin-top:-4px;" />';
                 }
                 elseif($data->isRejected()) {
                     // showDialogRejected
                     return '<a class="inter-active table-link action-show-status" data-status="'. $data->reject_reason . '" href="#">Не начислено</a>';
                 }
                 elseif($data->isPending()) {
                     // showDialogPending
                     return '<a class="inter-active table-link action-show-status" data-status="Пользователь ' . $data->referral_email . ' ещё не зарегистрировался на www.skiliks.com" href="#">В ожидании</a>';
                 }
            },
             'type' => 'raw'
            ],
        ]
    ]);
?>