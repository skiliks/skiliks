<?php $titles = [
    'ID профиля',
    'ID юзера',
    'Имя',
    'Фамилия',
    'Личный email',
    'Корпоративный email',
    'Дата регистрации',
    'Дата последнего посещения',
    'Тип аккаунта',
    'activation key',
    'Действия',
] ?>
<div class="row fix-top">
    <h2>Пользователи</h2>

    <?php $this->widget('CLinkPager',array(
        'header'         => '',
        'pages'          => $pager,
        'maxButtonCount' => 5, // максимальное вол-ко кнопок
    )); ?>

    Страница <?= $page ?> из <?= ceil($totalItems/$itemsOnPage) ?> (<?= $itemsOnPage ?> записей на странице из <?= $totalItems ?>)

    <br/>
    <br/>

    <table class="table table-hover">
        <thead>
        <tr>
            <?php foreach($titles as $title) :?>
            <th><?=$title?></th>
            <?php endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php /* @var $model Invoice */ ?>
        <?php $step = 12; $i = 0; ?>
        <?php foreach($profiles as $profile) : ?>
            <?php $i++ ?>
            <?php if($i === $step) : ?>
                    <tr>
                        <?php foreach($titles as $title) :?>
                            <th><?=$title?></th>
                        <?php endforeach ?>
                    </tr>
            <?php $i= 0 ?>
            <?php endif ?>
            <?php
                $isRegistered = ($profile->user->activationKey === '1');
                $rowBGcolor = '#ffffff';
                if (false === $isRegistered) {
                    $rowBGcolor = '#FFCC66';
                }
            ?>
            <tr class="orders-row" style="background-color: <?= $rowBGcolor ?>">
                <td><?= $profile->id ?></td>
                <td><?= $profile->user->id ?></td>
                <td>
                    <div style="max-width: 200px; overflow: auto;">
                        <?= $profile->firstname ?>
                    </div>
                </td>
                <td>
                    <div style="max-width: 200px; overflow: auto;">
                        <?= $profile->lastname ?>
                    </div>
                </td>
                <td>
                    <div style="max-width: 250px; overflow: auto;">
                        <?= $profile->email ?>
                    </div>
                </td>
                <td><?= (null !== $profile->user->getAccount() && $profile->user->isCorporate()) ? $profile->user->getAccount()->corporate_email : '--' ?></td>
                <td><?= date('Y-m-d H:i:s', strtotime($profile->user->createtime)) ?></td>
                <td><?= date('Y-m-d H:i:s', strtotime($profile->user->lastvisit)) ?></td>
                <td><?= $profile->user->getAccountName() ?></td>
                <td>
                    <div style="max-width: 200px; overflow: auto;">
                        <?= ($isRegistered) ? 'Ключ использован' : $profile->user->activationKey ?>
                    </div>
                </td>
                <td><a href="<?= $this->createAbsoluteUrl('admin_area/AdminPages/UpdatePassword', ['userId' => $profile->user->id]) ?>">Обновить пароль</a></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>