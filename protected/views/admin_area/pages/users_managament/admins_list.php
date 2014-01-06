
<h1> Админы: </h1>

<table class="table">
    <?php foreach ($admins as $admin) : ?>
        <tr><td>
            <a href="/admin_area/user/<?= $admin->id ?>/details">
                <?= $admin->profile->email?>
            </a>
        </td></tr>
    <?php endforeach ?>
</table>

<br/>

<h1> Разработчики (DEV): </h1>

<table class="table">
    <?php foreach ($devPermissions as $devPermission) : ?>
        <?php if (null == $devPermission->subordinate): ?>
            <tr><td>
                Subordinate is NULL.
            </td></tr>
        <?php else: ?>
            <tr><td>
                <a href="/admin_area/user/<?= $devPermission->subordinate->id ?>/details">
                    <?= $devPermission->subordinate->profile->email?>
                </a>
            </td></tr>
        <?php endif; ?>
    <?php endforeach ?>
</table>