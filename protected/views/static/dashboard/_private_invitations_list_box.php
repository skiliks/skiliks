<div id="private-invitations-list-box" class="transparent-boder wideblock">
    <?php

    $scoreRender = function(Invite $invite) {
        if(null !== $invite && !$invite->scenario->isLite()) {

            switch($invite->status) {
                case Invite::STATUS_PENDING :
                    return (string)$invite->getAcceptActionTag().' '.$invite->getDeclineActionTag();
                    break;

                case Invite::STATUS_COMPLETED :
                    if($invite->simulation !== null && false === $invite->simulation->isAllowedToSeeResults(Yii::app()->user->data())) {
                        return '<div style="line-height: 30px;">Результаты скрыты<div>';
                    }
                    else {
                        return $this->renderPartial('//global_partials/_simulation_stars', [
                            'simulation'     => $invite->simulation,
                            'isDisplayTitle' => false,
                            'isDisplayArrow' => false,
                            'isDisplayScaleIfSimulationNull' => false,
                        ],false);
                    }
                    break;

                case Invite::STATUS_ACCEPTED :
                case Invite::STATUS_IN_PROGRESS :
                    // start-full-simulation start-full-simulation-button table-link
                    return sprintf(
                        '<span class=" button-white button-white-hover inter-active label icon-arrow-blue reset-margin
                            action-open-full-simulation-popup accept-invite-button-width"
                            data-href="/simulation/promo/%s/%s"
                            >Начать</span>',
                        $invite->scenario->slug,
                        $invite->id
                    );
                    break;

                case Invite::STATUS_DECLINED :
                    return '<div style="line-height: 30px;">отклонено<div>';
                    break;

                case Invite::STATUS_DELETED :
                    return '<div style="line-height: 30px;">удалено<div>';
                    break;

                default :
                    return false;

            }
        }
        return false;
    };

    $this->widget('zii.widgets.grid.CGridView', [
        'dataProvider' => Invite::model()->searchByInvitedUserEmail(
            strtolower(Yii::app()->user->data()->profile->email),
            false
        ),
        'summaryText' => '',
        'emptyText' => '',
        'htmlOptions' => [
            'class' => 'contrast-table locator-contrast-table'
        ],
        'pager' => [
            'header'        => false,
            'firstPageLabel' => '<< начало',
            'prevPageLabel' => 'Назад',
            'nextPageLabel' => 'Вперед',
            'lastPageLabel' => 'конец >>',
        ],
        'columns' => [
            ['header' => Yii::t('site', Yii::t('site', 'Компания')), 'name' => "company", 'value' => 'Yii::t("site", $data->getCompanyOwnershipType()." ".$data->getCompanyName())'],
            ['header' => Yii::t('site', Yii::t('site', 'Позиция')), 'name' =>'vacancy_id', 'value' => '(Yii::t("site", $data->getVacancyLabel()) !== null) ? Yii::t("site", $data->getVacancyLabel()) : "-"', 'type' => 'text'],
            ['header' => Yii::t('site', Yii::t('site', 'Оценка')) , 'value' => '"Базовый менеджмент"'],
            [
                'header' => Yii::t('site', Yii::t('site', 'Дата')),
                'name'   => 'sent_time',
                'value'  => '$data->getDateForDashboard()',
                'type'   => 'raw'
            ],
            ['header' => Yii::t('site', Yii::t('site', 'Статус')) , 'value' => $scoreRender, 'type' => 'raw'],
        ]
    ]);
    ?>
</div>

<!-- accept-form { -->
<?php $this->renderPartial('accept_warning', []) ?>
<!-- accept-form } -->

<!-- decline-form { -->
<div class="locator-invite-decline-box"></div>
<!-- decline-form } -->