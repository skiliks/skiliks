<?php
$assetsUrl = $this->getAssetsUrl();
?>

<h1>
    <?php if (null !== $user->getAccount()) : ?>
        <?php if ($user->isCorporate()): ?>
            <img src="<?=$assetsUrl?>/img/bg-registration-corporate.png" />
        <?php else: ?>
            <img src="<?=$assetsUrl?>/img/bg-registration-personal.png" />
        <?php endif ?>

        <?= $user->profile->firstname ?>
        <?= $user->profile->lastname ?>
    <?php endif ?>
</h1>

<a href="/admin_area/user/<?= $user->id ?>/details">
    <i class="icon icon-arrow-left"></i> К аккаунту
</a>

<br/>
<br/>

<h4><?= ($user->account_corporate !== null) ? $user->account_corporate->getCompanyName() : '' ?></h4>
<?php

    $this->widget('zii.widgets.grid.CGridView', [
        'dataProvider' => UserReferral::model()->searchUserReferrals(
        $user->id
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
            ['header' => 'E-mail'            , 'name' => 'referral_email', 'value' => '$data->referral_email'],
            ['header' => 'Приглашен'         , 'name' => 'invited_at'    , 'value' => '$data->invited_at'    ],

            ['header' => 'Позьователь',
             'name'   => 'user_name',
             'value'  => function($data) {
                                             if($data->referral_id !== null) {
                                                $refer = YumUser::model()->findByPk($data->referral_id);
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