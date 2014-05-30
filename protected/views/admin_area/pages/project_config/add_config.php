
<?php /** @var ProjectConfig $config  */ ?>

<h1>
    <?= (null !== $config->id) ? 'Редактирование' : 'Добавление новой' ?>
    параметра <?= $config->alias ?></h1>

<br/>
<br/>

<a href="/admin_area/project_configs/list">
    &lt;- Назад, к списку конфигов
</a>

<br/>
<br/>
<br/>
<br/>

<?php $form = $this->beginWidget('CActiveForm', [
    'id'          => 'add-project-config-admin-form',
    'htmlOptions' => [
        'class'    => 'form-horizontal',
    ],
    'action'      => '/admin_area/project_configs/add',
]);
?>

<!-- Скрытые поля -->

<?php if (null !== $config->id): ?>
    <?= CHtml::hiddenField('add', true) ?>
    <?= CHtml::hiddenField('id', $config->id) ?>
<?php endif ?>

<!-- Псевдоним -->

<div class="control-group <?= $this->hasErrors($form, $config, 'alias') ?>">
    <?= $form->labelEx($config  , 'alias', ['class' => 'control-label']); ?>

    <div class="controls">
        <?= $form->textField($config, 'alias'); ?>
    </div>

        <span class="help-inline">
            <?= $form->error($config , 'alias'); ?>
        </span>
</div>

<!-- Тип -->

<div class="control-group <?= $this->hasErrors($form, $config, 'type') ?>">
    <?= $form->labelEx($config , 'type', ['class' => 'control-label']); ?>

    <div class="controls">
        <?= $form->dropDownList(
            $config,
            'type',
            ProjectConfig::$type
        ); ?>
    </div>

        <span class="help-inline">
            <?= $form->error($config, 'type'); ?>
        </span>
</div>

<!-- Использовать ли конфиг в симуляции -->

<div class="control-group <?= $this->hasErrors($form, $config, 'is_use_in_simulation') ?>">
    <?= $form->labelEx($config , 'is_use_in_simulation', ['class' => 'control-label']); ?>

    <div class="controls">
        <?= $form->dropDownList(
            $config,
            'is_use_in_simulation',
            ProjectConfig::$is_use_in_sim
        ); ?>
    </div>

        <span class="help-inline">
            <?= $form->error($config, 'is_use_in_simulation'); ?>
        </span>
</div>


<!-- Значение -->

<div class="control-group <?= $this->hasErrors($form, $config, 'value') ?>">
    <?= $form->labelEx($config  , 'value', ['class' => 'control-label']); ?>

    <div class="controls">
        <?= $form->textField($config, 'value'); ?>
    </div>

        <span class="help-inline">
            * Boolean: 0 - false, 1 - true <br/>
            * Float: 10,56 <br/>
            <?= $form->error($config , 'value'); ?>
        </span>
</div>

<!-- Описание -->

<div class="control-group <?= $this->hasErrors($form, $config, 'description') ?>">
    <?= $form->labelEx($config  , 'description', ['class' => 'control-label']); ?>

    <div class="controls">
        <?= $form->textArea($config, 'description', ['rows' => 6, 'style' => 'width: 600px']); ?>
    </div>

        <span class="help-inline">
            <?= $form->error($config , 'description'); ?>
        </span>
</div>

<!-- -->

<div class="form-actions">
    <?php echo CHtml::submitButton('Сохранить' , [
        'name'  => 'action',
        'class' => 'btn btn-success'
    ]); ?>
</div>

<?php $this->endWidget(); ?>








