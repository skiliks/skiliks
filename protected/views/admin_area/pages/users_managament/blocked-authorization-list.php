<? /* @var $users YumUser[] */ ?>
<h1> Пользователи: </h1>

<table class="table">

        <tr>
            <th>Email</th>
            <th>Полное имя</th>
            <th>Дата последнего логина</th>
        </tr>
    <?php foreach($users as $user) : ?>
        <tr>
            <td><?= $user->profile->email ?></td>
            <td><?= $user->profile->firstname.' '.$user->profile->lastname  ?></td>
            <td><?= date("d.m.Y H:i:s", $user->lastvisit) ?></td>
        </tr>
    <?php endforeach ?>
</table>