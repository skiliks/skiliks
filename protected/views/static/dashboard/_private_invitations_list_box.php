<h1 class="thetitle">Полученные приглашения</h1>

<div id="private-invitations-list-box" class="transparent-boder wideblock">
    <?php

    $scoreRender = function(Invite $invite) {
        if ($invite->status == Invite::STATUS_PENDING) {
            return (string)$invite->getAcceptActionTag().' '.$invite->getDeclineActionTag();
        }

        if (null !== $invite && false === $invite->isAllowedToSeeResults(Yii::app()->user->data())) {
            return 'Результаты скрыты';
        }

        return $this->renderPartial('//global_partials/_simulation_stars', [
            'simulation'     => $invite->simulation,
            'isDisplayTitle' => false,
            'isDisplayArrow' => false,
            'isDisplayScaleIfSimulationNull' => false,
        ],false);
    };

    $this->widget('zii.widgets.grid.CGridView', [
        'dataProvider' => Invite::model()->searchByInvitedUserEmail(
            strtolower(Yii::app()->user->data()->profile->email),
            [Invite::STATUS_PENDING, Invite::STATUS_COMPLETED]
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
            ['header' => Yii::t('site', Yii::t('site', 'Вакансия')), 'name' =>'vacancy_id', 'value' => 'Yii::t("site", $data->getVacancyLabel())'],
            ['header' => Yii::t('site', Yii::t('site', 'Оценка')) , 'value' => '"Базовый менеджмент"'],
            [
                'header' => Yii::t('site', Yii::t('site', 'Дата / Время')),
                'name' => 'sent_time',
                'value' => '$data->getUpdatedTime()->format("j/m/y") . " <time>" . $data->getUpdatedTime()->format("G:i") . "</time>"',
                'type' => 'raw'
            ],
            ['header' => Yii::t('site', Yii::t('site', 'Статус')) , 'value' => $scoreRender, 'type' => 'html'],
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