
<h1><?php echo Yii::t('site', 'Simulations') ?></h1>

Вам доступны к прохождению (accepted only invites):
<br/>
<br/>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => Invite::model()->searchByInvitedUserEmail(
        Yii::app()->user->data()->profile->email,
        Invite::STATUS_ACCEPTED
    ), //$dataProvider,
    'summaryText' => '',
    'pager' => [
        'header'        => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel' => '< назад',
        'nextPageLabel' => 'далее >',
        'lastPageLabel' => 'конец >>',
    ],
    'columns' => [
        ['header' => Yii::t('site', 'Company')    , 'value' => 'Yii::t("site", $data->ownerUser->getAccount()->ownership_type.$data->ownerUser->getAccount()->company_name)'],
        ['header' => Yii::t('site', 'Professional occupation')   , 'value' => 'Yii::t("site", $data->vacancy->professionalOccupation->label)'],
        ['header' => Yii::t('site', 'Specialization')   , 'value' => 'Yii::t("site", $data->vacancy->professionalSpecialization->label)'],
        ['header' => Yii::t('site', 'Date / time'), 'name' => 'sent_time'   , 'value' => '$data->getSentTime()->format("j/m/y G\h i\m")'],
        ['header' => Yii::t('site', 'Score')                                , 'value' => ''],
        ['header' => ''                                                     , 'value' => '"<a href=\"/simulation/legacy/promo/2/$data->id\">Начать</a>"'  , 'type' => 'html'],
    ]
]);
?>

<div id="simulation-details-pop-up"></div>

<script type="text/javascript">
    $(function(){
       $('#simulation-details-pop-up').dialog({
           modal: true,
           width: 940,
           minHeight: 600
       });
       $('#simulation-details-pop-up').dialog('close');

        $(".view-simulation-details-pop-up").click(function(event){
            event.preventDefault();
            $.ajax({
                url: $(this).attr('href'),
                success: function(data) {
                    $('#simulation-details-pop-up').html(data);
                    $('#simulation-details-pop-up').dialog('open');
                }
            });
        });
    });
</script>

<div style="height: 100px; width: 100%; float: none; clear: both;"></div>