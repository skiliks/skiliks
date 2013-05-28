<h1 class="title"><?php echo Yii::t('site', 'Available simulation') ?></h1>

<?php
$scoreRender = function(Invite $invite) {
    if ($invite->isComplete()) {
        return $this->renderPartial('//global_partials/_simulation_stars', [
            'simulation'     => $invite->simulation,
            'isDisplayTitle' => false,
            'isDisplayArrow' => false,
            'isDisplayScaleIfSimulationNull' => false,
        ],false);
    } elseif ($invite->isNotStarted()) {
        return sprintf(
            '<a href="/simulation/promo/%s/%s">Начать</a>',
            $invite->scenario->slug,
            $invite->id
        );
    } else {
        return Yii::t('site', Invite::$statusText[$invite->status]);
    }
};

$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => Invite::model()->searchByInvitedUserEmailForOwner(
        Yii::app()->user->data()->profile->email,
        false
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
        ['header' => Yii::t('site', 'Company')    , 'value' => 'Yii::t("site", $data->getCompanyOwnershipType(). " " .$data->getCompanyName())'],
        ['header' => Yii::t('site', 'Position')   , 'value' => 'Yii::t("site", $data->getVacancyLabel())'],
        [
            'header' => Yii::t('site', 'Simulation'),
            'name' => 'sent_time'   ,
            'value' => '$data->getFormattedScenarioSlug()'],
        ['header' => '', 'value' => $scoreRender , 'type' => 'html'],
    ]
]);
?>

<div id="simulation-details-pop-up"></div>

<script type="text/javascript">
    $(document).ready(function(){
        $(".pager").insertAfter("#yw0").css({"margin-top":"5px","text-align":"center"});
    });
</script>

<div style="height: 100px; width: 100%; float: none; clear: both;"></div>