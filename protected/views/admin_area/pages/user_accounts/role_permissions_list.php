
<?php $subtitles = [
    '1.1' => 'Общее',
    '2.1' => 'Заказы',
    '3.1' => 'Поддержка',
    '4.1' => 'Пользователи',
    '5.1' => 'Приглашения',
    '6.1' => 'Симуляции',
    '7.1' => 'Статистика',
    '8.1' => 'Управление',
];

var_dump($newRolePermissionsData);

?>

<h1>Список ролей</h1>

<form method="post" action="/admin_area/update-roles">

    <input class="btn btn-success" type="submit" name="save" value="Сохранить" style="margin-right: 50px;" />
    <input class="btn btn-success" type="submit" name="addRole" value="Добавить роль" />

    <br/>
    <br/>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th> </th>
                <?php foreach ($roles as $role) : ?>
                    <th><?= $role->title ?></th>
                <?php endforeach ?>
                <th>Добавить роль</th>
            </tr>
        </thead>
            <tr>
                <td>Имя новой роли</td>
                <?php foreach ($roles as $role) : ?>
                    <td></td>
                <?php endforeach ?>
                <td>
                    <input name="newRoleTitle" type="text" value="<?= $newRoleTitle ?>" />
                </td>
            </tr>
            <?php foreach ($roles as $role) : ?>
                <?php foreach ($actions as $action) : ?>
                    <?php if (isset($subtitles[$action->order_no])) : ?>
                        <?php // группы прав, заголовки групп ?>
                        <tr>
                            <td colspan="<?= count($roles) + 2 ?>">
                                <h5><?= $subtitles[$action->order_no] ?></h5>
                            </td>
                        </tr>
                    <?php endif ?>
                    <tr>
                        <!-- имя Права -->
                        <td>
                            <?= $action->order_no ?> <?= $action->subject ?>
                        </td>

                        <!-- галочки ролей -->
                        <?php foreach ($roles as $role) : ?>
                            <td>
                                <?php $isChecked = isset($rolePermission[$role->title][$action->order_no]); ?>

                                <?php // 'СуперАдмин' и 'Пользователь сайта'
                                // - это системные роли на уровне "может всё" и "всё запрещено"
                                // они должны и оставаться такими.
                                ?>
                                <?php $isReadonly = ('СуперАдмин' == $role->title || 'Пользователь сайта' == $role->title) ? true : false ?>

                                <input type="checkbox" name="rolePermission[<?= $role->id ?>][<?= $action->order_no ?>]"
                                    <?= ($isChecked) ? 'checked="checked"' : '' ?>
                                    <?= ($isReadonly) ? ' readonly="true" ' : '' ?>
                                    />
                            </td>
                        <?php endforeach; ?>

                        <!-- галочки "Добавить роль" -->
                        <td>
                            <?php $isChecked = (isset($newRolePermissionsData[$action->order_no]) && 'on' == $newRolePermissionsData[$action->order_no]); ?>
                            <input type="checkbox" name="newRolePermissions[<?= $action->order_no ?>]"
                                <?= ($isChecked) ? ' checked="checked" ' : ''; ?> />
                        </td>
                    <tr>
                <?php endforeach ?>
            <?php endforeach ?>
        <tbody></tbody>
    </table>

    <input class="btn btn-success" type="submit" name="updateActualRoles" value="Сохранить" style="margin-right: 50px;" />
    <input class="btn btn-success" type="submit" name="addRole" value="Добавить роль" />

</form>