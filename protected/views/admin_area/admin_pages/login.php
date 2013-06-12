<!--<form class="form-signin">-->

<?= CHtml::beginForm('/admin_area/login', 'post', ['class'=>'form-signin']) ?>
    <h2 class="form-signin-heading">Вход</h2>
    <!--<input type="text" class="input-block-level" placeholder="Email">-->
    <?= CHtml::activeTextField($model,'username', ['class'=>'input-block-level']); ?>
    <? if(null !== $model->getError('username')) : ?>
    <div class="alert alert-error">
        <?=$model->getError('username')?>
    </div>
    <? endif ?>
    <!--<input type="password" class="input-block-level" placeholder="Пароль">-->
    <?= CHtml::activePasswordField($model,'password',['class'=>'input-block-level']); ?>
    <? if(null !== $model->getError('password')) : ?>
        <div class="alert alert-error">
            <?=$model->getError('password')?>
        </div>
    <? endif ?>
    <label class="checkbox">
        <input type="checkbox" value="remember-me"> Запомнить меня
    </label>
    <?= CHtml::submitButton('Войти', ['class'=>'btn btn-large btn-primary']); ?>
    <!--<button class="btn btn-large btn-primary" type="submit">Войти</button>-->
<?= CHtml::endForm() ?>
<!--</form>-->