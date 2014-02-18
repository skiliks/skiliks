<h2 class="thetitle text-center">
    <?= Yii::t('site', '50% discount for all tariffs till March 31') ?>
</h2>
<?php
/* @var $tariffs  Tariff[] */
/* @var $tariff  Tariff */
/* @var $user  YumUser */
$lang = Yii::app()->getLanguage();
?>
<div class="tarifswrap">
<?php foreach ($tariffs as $tariff): ?>
    <?php if($tariff->isDisplayOnTariffsPage()): ?>
    <div class="nice-border onetariff tariff-<?=$tariff->slug?>">
        <div class="tariff-box radiusthree">
            <?php /*if($tariff->slug == "lite") : ?>
                <span class="show-free-tariff-img"></span>
            <? endif;*/ ?>
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
                        <p>
                            <?php // у LIte и Starter показывать не надо но только на русском ?>
                            <?php if ('ru' == $lang
                                && Tariff::SLUG_LITE != $tariff->slug
                                && Tariff::SLUG_STARTER != $tariff->slug ) : ?>
                                <?php echo Yii::t('site', $benefit)?>
                            <?php endif ?>
                            <?php if ('en' == $lang ) : ?>
                                <?php echo Yii::t('site', $benefit)?>
                            <?php endif ?>
                        </p>
                    <?php endforeach ?>
                </div>

                <?php if (false === $tariff->isUserCanChooseTariff($user)): ?>
                     <div class="subscribe-ti-tariff go-to-link">
                         <a class="light-btn" href="/tariffs/<?php echo $tariff->slug ?>">
                             <?php echo $tariff->getFormattedLinkLabel($user) ?>
                         </a>
                     </div>
                <?php else: ?>
                    <div class="subscribe-ti-tariff">
                        <a class="light-btn" href="#" data-tariff-slug="<?= $tariff->slug ?>">
                            <?php echo  Yii::t('site', 'Subscribe') ?>
                        </a>
                    </div>
                <?php endif ?>
            </div>
        </div>
    </div>
    <?php endif ?>
<?php endforeach ?>

    <p class="text-left text16 additional-text">
        <!--sup>*</sup> <?= Yii::t('site', 'Full price (without discount)') ?><br/-->
    </p>

    <?php if($user->isAuth() && $user->isCorporate()) : ?>
        <?php $this->renderPartial('//static/dashboard/partials/tariff-already-booked-popup', ['account'=>$user->account_corporate]) ?>
        <?php $this->renderPartial('//static/dashboard/partials/extend-tariff-popup', ['account'=>$user->account_corporate]) ?>
        <?php $this->renderPartial('//static/dashboard/partials/tariff-replace-now-popup', ['account'=>$user->account_corporate]) ?>
        <?php $this->renderPartial('//static/dashboard/partials/downgrade-tariff-popup', ['account'=>$user->account_corporate]) ?>
        <?php $this->renderPartial('//static/dashboard/partials/tariff-replace-if-zero-popup', ['account'=>$user->account_corporate]) ?>
    <?php endif ?>

    <div class="contwrap">
        <a class="light-btn feedback"><?= Yii::t('site', 'Send feedback') ?></a>
        <span class="social_networks">
            <?php $this->renderPartial('//global_partials/addthis', ['force' => true]) ?>
        </span>
    </div>
</div>