<h1 class="page-header text-center"><?= Yii::t('site', 'Pricing & Plans Monthly Rates') ?></h1>
<?php
/* @var $tariffs  Tariff[] */
/* @var $tariff  Tariff */
/* @var $user  YumUser */
$lang = Yii::app()->getLanguage();
?>
<div class="container-borders-4">
<?php foreach ($tariffs as $tariff): ?>
    <div class="block-border bg-yellow grid1 border-primary one-tariff">
           <header class="tariff-header font-white">
            <h5 class="text-center"><?php echo $tariff->label ?></h5>
            <div class="tariff-price <?= $lang ?>">
                <p>
                <?php if (floor($tariff->getPrice() / 1000)): ?>
                    <span><?php echo floor($tariff->getPrice() / 1000) ?></span>
                <?php endif ?>
                <?php echo $tariff->getPrice() % 1000 ?></p>
            </div>
            <strong class="border-primary bg-blue font-xslarge font-white display-ib tariff-save"><?php echo $tariff->getFormattedSafeAmount(Yii::t('site', 'Save ')) ?></strong>
           </header>

           <div class="pad-norm text-center">

                <strong class="border-primary font-4xlarge bg-yellow-light display-ib font-dark sim-amnt"><?php echo $tariff->getFormattedSimulationsAmount() ?></strong>

                <div class="grid-cell tariff-benefits">
                    <?php foreach (explode(', ', $tariff->benefits) as $benefit) : ?>
                        <strong class="font-large display-ib font-dark"><?php echo Yii::t('site', $benefit)?></strong>
                    <?php endforeach ?>
                </div>

                <?php if ($tariff->isUserCanChooseTariff($user)): ?>
                    <div class="text-center"><a class="btn btn-primary" href="/tariffs/<?php echo $tariff->slug ?>"><?php echo $tariff->getFormattedLinkLabel($user) ?></a></div>
                <?php else: ?>
                    <div class="text-center"><a class="btn btn-primary" href="/order-new/<?= $tariff->slug ?>"><?php echo  Yii::t('site', 'Subscribe') ?></a></div>
                <?php endif ?>
            </div>
    </div>
<?php endforeach ?>

    <p class="font-large text-right proxima-bold">
        <?php if ($lang == 'ru'): ?>
        <sup>*</sup> <a href="#" data-selected='Тарифы и оплата' class="feedback proxima-bold"><strong class="proxima-bold">Свяжитесь с нами,</strong></a> <strong>чтобы приобрести</strong>
        <?php endif; ?>
    </p>
    <div class="tariff-contacts"><a class="btn btn-primary feedback"><?= Yii::t('site', 'Send feedback') ?></a><span class="social_networks"><?php $this->renderPartial('//global_partials/addthis', ['force' => true]) ?></span>
    </div>
</div>