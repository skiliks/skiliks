<h1 class="page-header text-center"><?= Yii::t('site', 'Pricing & Plans Monthly Rates') ?></h1>
<?php
/* @var $tariffs  Tariff[] */
/* @var $tariff  Tariff */
/* @var $user  YumUser */
$lang = Yii::app()->getLanguage();
?>
<div class="container-borders-4">
<?php foreach ($tariffs as $tariff): ?>
    <div class="block-border bg-yellow grid1 border-primary">


        <div>
           <header class="tarif-header font-white">
            <h5 class="text-center"><?php echo $tariff->label ?></h5>
            <div class="price <?= $lang ?>">
                <p>
                <?php if (floor($tariff->getPrice() / 1000)): ?>
                    <span><?php echo floor($tariff->getPrice() / 1000) ?></span>
                <?php endif ?>
                <?php echo $tariff->getPrice() % 1000 ?></p>
            </div>
           </header>
            <div>

                <div><?php echo $tariff->getFormattedSafeAmount(Yii::t('site', 'Save ')) ?></div>

                <div><?php echo $tariff->getFormattedSimulationsAmount() ?></div>

                <div>
                    <?php foreach (explode(', ', $tariff->benefits) as $benefit) : ?>
                        <p><?php echo Yii::t('site', $benefit)?></p>
                    <?php endforeach ?>
                </div>

                <?php if ($tariff->isUserCanChooseTariff($user)): ?>
                     <div>
                         <a class="btn btn-primary" href="/tariffs/<?php echo $tariff->slug ?>">
                             <?php echo $tariff->getFormattedLinkLabel($user) ?>
                         </a>
                     </div>
                <?php else: ?>
                    <div>
                        <a class="btn btn-primary" href="/order-new/<?= $tariff->slug ?>">
                            <?php echo  Yii::t('site', 'Subscribe') ?>
                        </a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
<?php endforeach ?>

    <p>
        <?php if ($lang == 'ru'): ?>
        <sup>*</sup> <a href="#" data-selected='Тарифы и оплата' class="feedback"><strong>Свяжитесь с нами,</strong></a> чтобы приобрести
        <?php endif; ?>
    </p>
    <div><a class="btn btn-primary feedback"><?= Yii::t('site', 'Send feedback') ?></a>
    <span class="social_networks">
        <?php $this->renderPartial('//global_partials/addthis', ['force' => true]) ?>
    </span>
    </div>
</div>