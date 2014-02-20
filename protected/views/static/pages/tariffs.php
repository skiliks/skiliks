<h2 class="thetitle text-center"><?= Yii::t('site', 'Pricing & Plans Monthly Rates') ?></h2>
<?php
/* @var $user  YumUser */
$lang = Yii::app()->getLanguage();
?>
<div class="tarifswrap">

    <p class="text-left text16 additional-text">
        <?php if ($lang == 'ru'): ?>
        <sup>*</sup> Первый месяц использования <br/>
        <sup>**</sup> Симуляции по выбранному тарифу активны в течение месяца. По истечении месяца неиспользованные симуляции сгорают.
        <?php endif; ?>
    </p>
    <div class="contwrap"><a class="light-btn feedback"><?= Yii::t('site', 'Send feedback') ?></a>
    <span class="social_networks">
        <?php $this->renderPartial('//global_partials/addthis', ['force' => true]) ?>
    </span>
    </div>
</div>