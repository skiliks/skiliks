
<h1><?php echo Yii::t('site', 'Simulations') ?></h1>

Персональная

<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => Simulation::model()->search(Yii::app()->user->data()->id), //$dataProvider,
    'summaryText' => '',
    'pager' => [
        'header'        => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel' => '< назад',
        'nextPageLabel' => 'далее >',
        'lastPageLabel' => 'конец >>',
    ],
    'columns' => [
        ['header' => Yii::t('site', 'Id'), 'value' => '$data->id'],
        ['header' => ''                  , 'value' => '"<a class=\"view-simulation-details-pop-up\" href=\"/simulations/details/$data->id\">Подробно</a>"' , 'type' => 'html'],
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