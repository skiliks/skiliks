<br/>

<h1>
    <?= $siteUser->profile->firstname ?> <?= $siteUser->profile->lastname ?>
</h1>

<br/>

Почта: <?= $siteUser->profile->email ?>

<br/>
<br/>
<a href="/admin_area/user/<?= $siteUser->id ?>/details">
    <- Вернуться назад, к данным аккаунта пользователя
</a>

<br/>
<br/>
<a href="/admin_area/user/<?= $siteUser->id ?>/details">
    <- Вернуться назад, к данным аккаунта пользователя
</a>

<br/>
<hr/>
<br/>
<form class="form-horizontal" method="post">
    <div class="form-group">
        <label class="control-label" style="margin-right: 20px;">Новый пароль:</label>
        <input class="form-control" type="text" name="new_password" />
    </div>

    <br/>
    <br/>

    <input class="btn btn-primary" type="submit" value="Сохранить" style="margin-left: 300px;">
</form>
