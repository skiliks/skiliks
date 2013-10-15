<?php $titles = [
    'ID юзера',
    'ID профиля',
    'Имя',
    'Фамилия',
    'Личный email',
    'Корпоративный email',
    'Дата регистрации',
    'Дата последнего посещения',
    'Аккаунт',
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

                $accountTypeIcon = 'icon-ban-circle icon-white';
                $accountTypeLabelType = 'label label-inverse';

                if ($profile->user->isPersonal()){
                    $accountTypeIcon = 'icon-user icon-white';
                    $accountTypeLabelType = 'label label-warning';
                } elseif ($profile->user->isCorporate()){
                    $accountTypeIcon = 'icon-briefcase icon-white';
                    $accountTypeLabelType = 'label label-info';
                };
            ?>
            <tr class="orders-row" style="background-color: <?= $rowBGcolor ?>">
                <td>
                    <i class="icon icon-user" style="opacity: 0.25"></i>
                        <a href="/admin_area/user/<?= $profile->user->id ?>/details">
                        <?= $profile->user->id ?>
                        </a>
                </td>
                <td>
                    <i class="icon icon-home" style="opacity: 0.10"></i>
                    <span style="color: #ccc"><?= sprintf('%s', $profile->id) ?></span>
                </td>
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
                        <?php
                            $opacity = 0.25;
                            if ($profile->user->isPersonal()) { $opacity = 0.5; }
                            if ($profile->user->isCorporate()) { $opacity = 0.25; }
                        ?>
                        <i class="icon icon-user" style="opacity: <?= $opacity ?>"></i>
                        </i><?= $profile->email ?>
                    </div>
                </td>

                <td>
                    <div style="max-width: 250px; overflow: auto;">
                        <?php
                        $opacity = 0;
                        if ($profile->user->isCorporate()) { $opacity = 0.5; }
                        ?>
                        <i class="icon icon-briefcase" style="opacity: <?= $opacity ?>"></i>
                        <?= ($profile->user->isCorporate()) ? $profile->email : '--' ?>
                    </div>
                </td>

                <td>
                    <?= date('Y-m-d H:i:s', $profile->user->createtime) ?>
                </td>
                <td>
                    <?= date('Y-m-d H:i:s', $profile->user->lastvisit) ?>
                </td>
                <td>
                    <div class="<?= $accountTypeLabelType ?>" style="padding: 5px;">
                        <i class="<?= $accountTypeIcon ?>"></i> <?= $profile->user->getAccountName() ?>
                    </div>
                </td>
                <td>
                    <div style="max-width: 200px; overflow: auto;">
                        <?= ($isRegistered) ? 'Аккаунт активирован' : $profile->user->activationKey ?>
                    </div>
                </td>
                <td>
                    <a class="btn btn-success"
                        href="<?= $this->createAbsoluteUrl('admin_area/AdminPages/UpdatePassword', ['userId' => $profile->user->id]) ?>">
                        <i class="icon icon-pencil icon-white"></i>&nbsp;
                        Изменить пароль</a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>