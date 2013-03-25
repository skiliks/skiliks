<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => Invite::model()->search(Yii::app()->user->data()->id), //$dataProvider,
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
        ['header' => Yii::t('site', 'Full name')  , 'name' => 'name'        , 'value' => '$data->getFullname()'],
        ['header' => Yii::t('site', 'Position')   , 'name' => 'vacancy_id'  , 'value' => 'Yii::t("site", $data->vacancy->label)'],
        ['header' => Yii::t('site', 'Status')     , 'name' => 'status'      , 'value' => 'Yii::t("site", $data->getStatusText())'],
        ['header' => Yii::t('site', 'Date / time'), 'name' => 'sent_time'   , 'value' => '$data->getSentTime()->format("j/m/y G\h i\m")'],
        ['header' => Yii::t('site', 'Score')                                , 'value' => '"-"'],
        ['header' => ''                                                     , 'value' => '"<a href=\"/dashboard/invite/remove/$data->id\">удалить</a>"'                , 'type' => 'html'],
        ['header' => ''                                                     , 'value' => '"<a class=\"edit-invite\" href=\"$data->id&&$data->vacancy_id\" title=\"$data->firstname, $data->lastname\">исправить</a>"', 'type' => 'html'],
        ['header' => ''                                                     , 'value' => '"<a href=\"/dashboard/invite/resend/$data->id\">отправить ещё раз</a>"' , 'type' => 'html'],
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
            });
        }

        $('.invites-smallmenu-switcher').each(function(){
            // move links from last 3 TD to pop-up sub-menu
            $(this).next().append($(this).parent().parent().find('td:eq(6)').html() + $(this).parent().parent().find('td:eq(7)').html() + $(this).parent().parent().find('td:eq(8)').html() );

            // remove links from last 3 TD
            $(this).parent().parent().find('td:eq(6)').html('');
            $(this).parent().parent().find('td:eq(7)').html('');
            $(this).parent().parent().find('td:eq(8)').html('');

            // make links (now they in pop-up sub-menu) visible
            $('.items td a').show();
            $('.items td a').css({display: 'block'});
        });

        // setup sub-menu switcher behaviour
        $('.invites-smallmenu-switcher').click(function(){
            $(this).next().toggle();
        });
    });
</script>

<?php // edit invite pop-up form { ?>
    <?php //PHP: ?>
        <?php $this->renderPartial('_edit-invite-pop-up-form', [
            'invite'    => $inviteToEdit,
            'vacancies' => $vacancies,
        ]) ?>

    <?php // java-script: ?>
        <?php if (0 < count($inviteToEdit->getErrors())): ?>
            $( ".form-invite-message-editor").dialog('open');
        <?php endif; ?>
<?php // edit invite pop-up form } ?>