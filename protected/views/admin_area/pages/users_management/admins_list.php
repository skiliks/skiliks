

<h1> СупераАдмины: </h1>

<table class="table">
    <?php foreach ($superAdmins as $superAdmin) : ?>
        <tr><td>
            <a href="/admin_area/user/<?= $superAdmin->id ?>/details">
                <?= $superAdmin->profile->email?>
            </a>
        </td></tr>
    <?php endforeach ?>
</table>

<br/>

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


