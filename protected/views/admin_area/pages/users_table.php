<? $titles = [
    'ID профиля',
    'ID юзера',
    'Имя',
    'Фамилия',
    'Личный email',
    'Корпоративный email',
    'Дата регистрации',
    'Дата последнего посещения',
] ?>
<div class="row fix-top">
    <h2>Пользователи</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <? foreach($titles as $title) :?>
            <th><?=$title?></th>
            <? endforeach ?>
        </tr>
        </thead>
        <tbody>
        <? /* @var $model Invoice */ ?>
        <? $step = 12; $i = 0; ?>
        <? foreach($profiles as $profile) : ?>
            <? $i++ ?>
            <? if($i === $step) : ?>
                    <tr>
                        <? foreach($titles as $title) :?>
                            <th><?=$title?></th>
                        <? endforeach ?>
                    </tr>
            <? $i= 0 ?>
            <? endif ?>
            <tr class="orders-row">
                <td><?= $profile->id ?></td>
                <td><?= $profile->user->id ?></td>
                <td><?= $profile->firstname ?></td>
                <td><?= $profile->lastname ?></td>
                <td><?= $profile->email ?></td>
                <td><?= (null !== $profile->user->getAccount() && $profile->user->isCorporate()) ? $profile->user->getAccount()->corporate_email : '--' ?></td>
                <td><?= date('Y-m-d H:i:s', strtotime($profile->user->createtime)) ?></td>
                <td><?= date('Y-m-d H:i:s', strtotime($profile->user->lastvisit)) ?></td>
            </tr>
        <? endforeach ?>
        </tbody>
    </table>
</div>