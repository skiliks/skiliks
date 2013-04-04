<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => Invite::model()->search(Yii::app()->user->data()->id), //$dataProvider,
    'summaryText' => '',
    'pager' => [
        'header'        => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel' => 'Назад',
        'nextPageLabel' => 'Вперед',
        'lastPageLabel' => 'конец >>',
    ],
    'columns' => [
        ['header' => ''                           , 'value' => '', 'type' => 'html'],
        ['header' => Yii::t('site', 'Full name')  , 'name' => 'name'        , 'value' => '$data->getFullname()'],
        ['header' => Yii::t('site', 'Position')   , 'name' => 'vacancy_id'  , 'value' => 'Yii::t("site", $data->vacancy->label)'],
        ['header' => Yii::t('site', 'Status')     , 'name' => 'status'      , 'value' => 'Yii::t("site", $data->getStatusText())'],
        ['header' => Yii::t('site', 'Date / time'), 'name' => 'sent_time'   , 'value' => '$data->getSentTime()->format("j/m/y G\h i\m")'],
        ['header' => Yii::t('site', 'Score')                                , 'value' => '"-"'],
        ['header' => ''                                                     , 'value' => '"<a href=\"/dashboard/invite/remove/$data->id\">Удалить</a>"'                , 'type' => 'html'],
        ['header' => ''                                                     , 'value' => '"<a class=\"edit-invite\" href=\"$data->id&&$data->vacancy_id\" title=\"$data->firstname, $data->lastname\">Исправить</a>"', 'type' => 'html'],
        ['header' => ''                                                     , 'value' => '"<a href=\"/dashboard/invite/resend/$data->id\">Отправить ещё раз</a>"' , 'type' => 'html'],
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
                    '<a class="invites-smallmenu-switcher"></a><div class="invites-smallmenu-item" ></div><span class="topline"></span>'
                );
            });
        }

        // remove last 3 TH
        $('.items').find('th:eq(8)').remove();
        $('.items').find('th:eq(7)').remove();
        $('.items').find('th:eq(6)').remove();

        $('.invites-smallmenu-switcher').each(function(){
            // move links from last 3 TD to pop-up sub-menu
            $(this).next().append(
                $(this).parent().parent().find('td:eq(6)').html()
                + $(this).parent().parent().find('td:eq(7)').html()
                + $(this).parent().parent().find('td:eq(8)').html()
            );

            // remove last 3 TD
            $(this).parent().parent().find('td:eq(8)').remove();
            $(this).parent().parent().find('td:eq(7)').remove();
            $(this).parent().parent().find('td:eq(6)').remove();

            // make links (now they in pop-up sub-menu) visible
            $('.items td a').show();

        });

        // setup sub-menu switcher behaviour
        $('.invites-smallmenu-switcher').click(function(){

            var isVisible = $(this).next().is(":visible");

            // click must close all other open "small-menu"
            $('tr.selected-sk .invites-smallmenu-item').hide();
            $('tr.selected-sk').removeClass('selected-sk');

            // after removeClass('selected-sk') and hide(), function this.toggle() will always shown element,
            // so I store menu state before I hide all menus and use isVisible to determine show or hide current menu
            if (isVisible) {
                var a = $(this).next().hide();
                $(this).parent().parent().removeClass('selected-sk');
            } else {
                var a = $(this).next().toggle();
                $(this).parent().parent().toggleClass('selected-sk');
            }
        });

        $('.invites-smallmenu-item').click(function(){
            $(this).hide();
            $(this).parent().parent().removeClass('selected-sk');
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
<?php // edit invite pop-up form } ?>