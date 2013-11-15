<h2 class="thetitle text-center"><?= Yii::t('site', 'Pricing & Plans Monthly Rates') ?></h2>
<?php
/* @var $tariffs  Tariff[] */
/* @var $tariff  Tariff */
/* @var $user  YumUser */
$lang = Yii::app()->getLanguage();
?>
<div class="tarifswrap">
<?php foreach ($tariffs as $tariff): ?>
    <div class="nice-border onetariff tariff-<?=$tariff->slug?>">
        <div class="tariff-box radiusthree">
            <?php if($tariff->slug == "lite") : ?>
                <span class="show-free-tariff-img"></span>
            <? endif; ?>
                <label class="tarifname"><div class="label_div"><?php echo $tariff->label ?></div></label>
            <div class="price <?= $lang ?>">
                <p>
                    <?php if (floor($tariff->getPrice() / 1000)): ?>
                        <span><?php echo floor($tariff->getPrice() / 1000) ?></span>
                    <?php endif ?>
                    <?php echo $tariff->getPrice() % 1000 ?>
                </p>
            </div>
            <div class="tarifwrap <?= $lang ?>">

                <?php if($tariff->getSaveAmount() != 0.00) : ?>
                    <div class="brightblock">
                        <?php echo $tariff->getFormattedSafeAmount(Yii::t('site', 'Save ')) ?>
                    </div>
                <?php endif; ?>

                <div class="simulations-amount lightblock">
                    <?php echo $tariff->getFormattedSimulationsAmount() ?>
                </div>

                <div class="benefits">
                    <?php foreach (explode(', ', $tariff->benefits) as $benefit) : ?>
                        <p><?php echo Yii::t('site', $benefit)?></p>
                    <?php endforeach ?>
                </div>

                <?php if ($tariff->isUserCanChooseTariff($user)): ?>
                     <div class="subscribe-ti-tariff">
                         <a class="light-btn" href="/tariffs/<?php echo $tariff->slug ?>">
                             <?php echo $tariff->getFormattedLinkLabel($user) ?>
                         </a>
                     </div>
                <?php else: ?>
                    <div class="subscribe-ti-tariff">
                        <a class="light-btn" href="/payment/order/<?= $tariff->slug ?>">
                            <?php echo  Yii::t('site', 'Subscribe') ?>
                        </a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
<?php endforeach ?>

    <p class="text-left text16 ProximaNova-Bold additional-text">
        <?php if ($lang == 'ru'): ?>
        <sup>*</sup> Первый месяц использования
        <?php endif; ?>
    </p>
    <div class="contwrap"><a class="light-btn feedback"><?= Yii::t('site', 'Send feedback') ?></a>
    <span class="social_networks">
        <?php $this->renderPartial('//global_partials/addthis', ['force' => true]) ?>
    </span>
    </div>
</div>