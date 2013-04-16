<h1 class="title"><?php echo Yii::t('site', 'Available simulation') ?></h1>

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => Invite::model()->searchByInvitedUserEmail(
        Yii::app()->user->data()->profile->email,
        Invite::STATUS_ACCEPTED
    ),
    'summaryText' => '',
    'pager' => [
        'header'        => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel' => '< назад',
        'nextPageLabel' => 'далее >',
        'lastPageLabel' => 'конец >>',
    ],
    'columns' => [
        ['header' => Yii::t('site', 'Company')    , 'value' => 'Yii::t("site", $data->ownerUser->getAccount()->ownership_type. " " .$data->ownerUser->getAccount()->company_name)'],
        ['header' => Yii::t('site', 'Position')   , 'value' => 'Yii::t("site", $data->vacancy->professionalSpecialization->label)'],
        [
            'header' => Yii::t('site', 'Simulation'),
            'name' => 'sent_time'   ,
            'value' => '($data->scenario->slug === Scenario::TYPE_LITE ? Yii::t("site","Lite verion") : "") . "Базовый менеджмент"'],
        ['header' => ''                                                     , 'value' => '"<a href=\"/simulation/promo/{$data->scenario->slug}/$data->id\">Начать</a>"'  , 'type' => 'html'],
    ]
]);
?>

<div id="simulation-details-pop-up"></div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".pager").insertAfter("#yw0").css({"margin-top":"5px","text-align":"center"});
    });
    /*$(function(){
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
    });*/
</script>

<div style="height: 100px; width: 100%; float: none; clear: both;"></div>