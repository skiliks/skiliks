<h1 class="thetitle">Полученные приглашения</h1>

<div id="private-invitations-list-box" class="transparent-boder wideblock">
    <?php

    $scoreRender = function(Invite $invite) {
        if(!$invite->scenario->isLite()) {

            if ($invite->status == Invite::STATUS_PENDING) {
                return (string)$invite->getAcceptActionTag().' '.$invite->getDeclineActionTag();
            }

            if (null !== $invite && $invite->status == Invite::STATUS_COMPLETED && false === $invite->isAllowedToSeeResults(Yii::app()->user->data())) {
                return '<div style="line-height: 30px;">Результаты скрыты<div>';
            }

            if (null !== $invite && Invite::STATUS_ACCEPTED != $invite->status && Invite::STATUS_COMPLETED != $invite->status && $invite->scenario->isFull()) {
                //$return = '<a data-href="'.$invite->id.'" class="start-full-simulation start-full-simulation-button" href="#">Начать</a>';
                //echo $return;
                return sprintf(
                    "<a class=\"start-full-simulation start-full-simulation-button\" data-href=\"/simulation/promo/%s/%s\" href=\"#\">Начать</a>",
                    $invite->scenario->slug,
                    $invite->id
                );
            }

            if (null !== $invite && Invite::STATUS_COMPLETED == $invite->status && $invite->scenario->isFull()) {
                //$return = '<a data-href="'.$invite->id.'" class="start-full-simulation start-full-simulation-button" href="#">Начать</a>';
                //echo $return;
                    return $this->renderPartial('//global_partials/_simulation_stars', [
                        'simulation'     => $invite->simulation,
                        'isDisplayTitle' => false,
                        'isDisplayArrow' => false,
                        'isDisplayScaleIfSimulationNull' => false,
                    ],false);
            }


            return $this->renderPartial('//global_partials/_simulation_stars', [
                'simulation'     => $invite->simulation,
                'isDisplayTitle' => false,
                'isDisplayArrow' => false,
                'isDisplayScaleIfSimulationNull' => false,
            ],false);
        }
        else return false;
    };

    $this->widget('zii.widgets.grid.CGridView', [
        'dataProvider' => Invite::model()->searchByInvitedUserEmail(
            strtolower(Yii::app()->user->data()->profile->email),
            false
        ),
        'summaryText' => '',
        'emptyText' => '',
        'pager' => [
            'header'        => false,
            'firstPageLabel' => '<< начало',
            'prevPageLabel' => 'Назад',
            'nextPageLabel' => 'Вперед',
            'lastPageLabel' => 'конец >>',
        ],
        'columns' => [
            ['header' => Yii::t('site', Yii::t('site', 'Компания')), 'name' => "company", 'value' => 'Yii::t("site", $data->getCompanyOwnershipType()." ".$data->getCompanyName())'],
            ['header' => Yii::t('site', Yii::t('site', 'Вакансия')), 'name' =>'vacancy_id', 'value' => '(Yii::t("site", $data->getVacancyLabel()) !== null) ? Yii::t("site", $data->getVacancyLabel()) : "-"'],
            ['header' => Yii::t('site', Yii::t('site', 'Оценка')) , 'value' => '"Базовый менеджмент"'],
            [
                'header' => Yii::t('site', Yii::t('site', 'Дата / Время')),
                'name' => 'sent_time',
                'value' => '$data->getUpdatedTime()->format("j/m/y") . " <time>" . $data->getUpdatedTime()->format("G:i") . "</time>"',
                'type' => 'raw'
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
<div id="invite-decline-form"></div>
<!-- decline-form } -->

<script type="text/javascript">
    $(function(){
        // setup sub-menu switcher behaviour
        $('.invites-smallmenu-switcher').click(function(){
            $(this).next().toggle();
            Cufon.refresh();
        });

        // decline dialog {
        $.ajax({
            url: '/dashboard/decline-invite/validation',
            type: 'POST',
            success: function(data) {
                $('#invite-decline-form').html(data.html).hide();

                /*
                $('#invite-decline-form').dialog({
                    width: 500,
                    modal: true
                });
                */

                //$('#invite-decline-form').parent().addClass('nice-border');
                //$('#invite-decline-form').parent().addClass('backgroud-rich-blue');

                //$('#invite-decline-form').dialog('close');

                $('.decline-link').click(function(event){
                    event.preventDefault();
                    $('#invite-decline-form').find('input#DeclineExplanation_invite_id').val($(this).attr('title'));

                    $('#invite-decline-form').show();
                    //$('#invite-decline-form').dialog('open');
                });
            }
        })
        // decline dialog }
     });
</script>