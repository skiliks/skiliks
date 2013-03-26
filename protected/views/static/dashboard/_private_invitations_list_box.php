<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => Invite::model()->searchByInvitedUserEmail(Yii::app()->user->data()->profile->email), //$dataProvider,
    'summaryText' => '',
    'pager' => [
        'header'        => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel' => '< назад',
        'nextPageLabel' => 'далее >',
        'lastPageLabel' => 'конец >>',
    ],
    'columns' => [
        ['header' => ''                           , 'value' => '', 'type' => 'html'],
        ['header' => Yii::t('site', 'Company')    , 'value' => 'Yii::t("site", $data->ownerUser->getAccount()->ownership_type.$data->ownerUser->getAccount()->company_name)'],
        ['header' => Yii::t('site', 'Professional occupation')   , 'value' => 'Yii::t("site", $data->vacancy->professionalOccupation->label)'],
        ['header' => Yii::t('site', 'Specialization')   , 'value' => 'Yii::t("site", $data->vacancy->professionalSpecialization->label)'],
        ['header' => Yii::t('site', 'Status')     , 'name' => 'status'      , 'value' => 'Yii::t("site", $data->getStatusText())'],
        ['header' => Yii::t('site', 'Date / time'), 'name' => 'sent_time'   , 'value' => '$data->getSentTime()->format("j/m/y G\h i\m")'],
        ['header' => Yii::t('site', 'Score')                                , 'value' => ''],
        ['header' => ''                                                     , 'value' => '"<a href=\"/dashboard/accept-invite/$data->code\">принять</a>"' , 'type' => 'html'],
        ['header' => ''                                                     , 'value' => '"<a href=\"/dashboard/decline-invite/$data->code\">отказать</a>"', 'type' => 'html'],
        ['header' => ''                                                     , 'value' => '"<a href=\"/dashboard/invite/remove/$data->id\">удалить</a>"'                , 'type' => 'html'],
    ]
]);
?>

<script type="text/javascript">
    $(function(){
        // hide links from last 3 TD to pop-up sub-menu
        $('.items td a').hide();

        // append pop-up sub-menu
        if (2 < $('.items tr').length || '' != $('.items tr:eq(1) td:eq(3)').text()) { //fix for empty list
            $('.items tr').each(function(){
                $(this).find('td:eq(0)').html(
                    '<a class="invites-smallmenu-switcher">меню</a> &nbsp;  &nbsp; <div class="invites-smallmenu-item" ></div>'
                );

                // move links to status column
                if (0 < $(this).find('td:eq(6)').text().length) {
                    $(this).find('td:eq(6)').append($(this).find('td:eq(7)').html() + $(this).find('td:eq(8)').html());
                }
                // clean up 'accept' and 'decline' columns
                $(this).find('td:eq(7)').html('');
                $(this).find('td:eq(8)').html('');
            });
        }

        $('.invites-smallmenu-switcher').each(function(){
            // move links from last TD to pop-up sub-menu
            $(this).next().append($(this).parent().parent().find('td:eq(9)').html());

            // remove links from last TD
            $(this).parent().parent().find('td:eq(9)').html('');

            // make links (now they in pop-up sub-menu) visible
            $('.items td a').show();

        });

        // setup sub-menu switcher behaviour
        $('.invites-smallmenu-switcher').click(function(){
            $(this).next().toggle();
        });
    });
</script>