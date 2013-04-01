<h1 class="thetitle">Полученные приглашения</h1>

<div id="private-invitations-list-box" class="transparent-boder wideblock">
    <?php
    $this->widget('zii.widgets.grid.CGridView', [
        'dataProvider' => Invite::model()->searchByInvitedUserEmail(
            Yii::app()->user->data()->profile->email,
            [Invite::STATUS_PENDING, Invite::STATUS_COMPLETED]
        ), //$dataProvider,
        'summaryText' => '',
        'pager' => [
            'header'        => false,
            'firstPageLabel' => '<< начало',
            'prevPageLabel' => 'Назад',
            'nextPageLabel' => 'Вперед',
            'lastPageLabel' => 'конец >>',
        ],
        'columns' => [
            ['header' => Yii::t('site', Yii::t('site', 'Компания'))    , 'value' => 'Yii::t("site", $data->ownerUser->getAccount()->ownership_type." ".$data->ownerUser->getAccount()->company_name)'],
            ['header' => Yii::t('site', Yii::t('site', 'Вакансия')), 'name' =>'vacancy_id', 'value' => 'Yii::t("site", $data->vacancy->label)'],
            ['header' => Yii::t('site', Yii::t('site', 'Оценка')) , 'value' => '"Базовый менеджмент"'],
            ['header' => Yii::t('site', Yii::t('site', 'Дата / Время')), 'name' => 'sent_time'   , 'value' => '$data->getSentTime()->format("j/m/y G:i")'],
            ['header' => Yii::t('site', Yii::t('site', 'Статус')) , 'value' => '$data->getSimulationResultsTag()', 'type' => 'html'],
        ]
    ]);
    ?>
</div>

<!-- decline-form { -->
<div id="invite-decline-form"></div>
<!-- decline-form } -->

<script type="text/javascript">
    $(function(){
        // setup sub-menu switcher behaviour
        $('.invites-smallmenu-switcher').click(function(){
            $(this).next().toggle();
        });

        // decline dialog {
        $.ajax({
            url: '/dashboard/decline-invite/validation',
            type: 'POST',
            success: function(data) {
                $('#invite-decline-form').html(data.html);

                $('#invite-decline-form').dialog({
                    width: 500,
                    modal: true
                });

                $('#invite-decline-form').parent().addClass('nice-border');
                $('#invite-decline-form').parent().addClass('backgroud-rich-blue');

                $('#invite-decline-form').dialog('close');

                $('.decline-link').click(function(event){
                    event.preventDefault();
                    $('#invite-decline-form input#DeclineExplanation_invite_id').val($(this).attr('title'));

                    $('#invite-decline-form')
                    $('#invite-decline-form').dialog('open');
                });
            }
        })
        // decline dialog }
     });
</script>