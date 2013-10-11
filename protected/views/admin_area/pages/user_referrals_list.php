<?php
$assetsUrl = $this->getAssetsUrl();
?>

<h1>
    <?php if (null !== $user->getAccount()) : ?>
        <?php if ($user->isCorporate()): ?>
            <img src="<?=$assetsUrl?>/img/bg-registration-corporate.png" />
        <?php else: ?>
            <img src="<?=$assetsUrl?>/img/bg-registration-personal.png" />
        <?php endif ?>

        <?= $user->profile->firstname ?>
        <?= $user->profile->lastname ?>
    <?php endif ?>
</h1>

<a href="/admin_area/user/<?= $user->id ?>/details">
    <i class="icon icon-arrow-left"></i> К аккаунту
</a>

<br/>
<br/>
<h4><?= ($user->account_corporate !== null) ? $user->account_corporate->getCompanyName() : '' ?></h4>
<? $this->renderPartial("/admin_area/pages/_user_referrals_list", ['dataProvider' => $dataProvider]); ?>