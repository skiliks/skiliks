<div class="pull-right">
    <?php $form=$this->beginWidget('CActiveForm', ['htmlOptions' => array('class' => 'form-inline')]); ?>
    <?php echo $form->errorSummary($email); ?>
        <?php echo $form->label($email,'domain'); ?>
        <?php echo $form->textField($email,'domain', array('class' => 'input-small')) ?>
        <?php echo CHtml::submitButton('Добавить', array('class' => 'btn btn-primary')); ?>
    <?php $this->endWidget(); ?>
</div>
<?php
$this->widget('zii.widgets.grid.CGridView', [
    'dataProvider' => $dataProvider,
    'summaryText' => '',
    'emptyText'   => '',
    'ajaxUpdate'=>false,
    'nullDisplay' => '',
    'filter'      => new FreeEmailProvider(),
    'pager' => [
        'header'         => false,
        'firstPageLabel' => '<< начало',
        'prevPageLabel'  => 'Назад',
        'nextPageLabel'  => 'Вперед',
        'lastPageLabel'  => 'конец >>',
    ],
    'columns' => [
        ['header' => 'ID', 'name' => 'id', 'value' => '$data->id'],
        ['header' => 'Домен', 'name' => 'domain', 'value' => '$data->domain']
    ]
]);