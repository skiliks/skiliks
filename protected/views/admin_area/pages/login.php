<br/>
<br/>
<br/>

<?= CHtml::beginForm('/admin_area/login', 'post', ['class'=>'form-signin']) ?>
    <h2 class="form-signin-heading">Вход</h2>

    <?= CHtml::activeTextField($model,'username', ['class'=>'input-block-level']); ?>

    <?php if(null !== $model->getError('username')) : ?>
        <div class="alert alert-error">
            <?=$model->getError('username')?>
        </div>
    <?php endif ?>

    <?= CHtml::activePasswordField($model,'password',['class'=>'input-block-level']); ?>

    <?php if(null !== $model->getError('password')) : ?>
        <div class="alert alert-error">
            <?=$model->getError('password')?>
        </div>
    <?php endif ?>

    <label class="checkbox">
        <input type="checkbox" value="remember-me"> Запомнить меня
    </label>

    <?= CHtml::submitButton('Войти', ['class'=>'btn btn-large btn-primary']); ?>

<?= CHtml::endForm() ?>
