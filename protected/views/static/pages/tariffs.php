<h2 class="thetitle text-center">Тарифные планы: цена подписки в месяц</h2>
<?php
/* @var $tariffs  Tariff[] */
/* @var $tariff  Tariff */
/* @var $user  YumUser */
?>
<div class="tarifswrap">
<?php foreach ($tariffs as $tariff): ?>
    <div class="nice-border onetariff">
        <div class="tariff-box radiusthree">
            <label class="tarifname"><?php echo $tariff->label ?></label>
            <div class="price">
                <p><?php if (floor($tariff->price / 1000)): ?>
                    <span><?php echo floor($tariff->price / 1000) ?></span>
                <?php endif ?>
                <?php echo $tariff->price % 1000 ?></p>
            </div>
            <div class="tarifwrap">

                <div class="brightblock">
                    <?php echo $tariff->getFormattedSafeAmount('Экономия ') ?>
                </div>

                <div class="simulations-amount lightblock">
                    <?php echo $tariff->getFormattedSimulationsAmount() ?>
                </div>

                <div class="benefits">
                    <?php foreach (explode(',', $tariff->benefits) as $benefit) : ?>
                        <p><?php echo $benefit?></p>
                    <?php endforeach ?>
                </div>

                <?php if($tariff->isUserCanChooseTariff($user)): ?>
                     <div class="subscribe-ti-tariff">
                         <a class="light-btn" href="/tariffs/<?php echo $tariff->slug ?>">
                             <?php echo $tariff->getFormattedLinkLabel($user) ?>
                         </a>
                     </div>
                <?php endif ?>
            </div>
        </div>
    </div>
<?php endforeach ?>
    <p class="text-right text16"><sup>*</sup> <strong>Свяжитесь с нами,</strong> чтобы приобрести</p>
    <div class="contwrap"><a class="light-btn feedback">Обратная связь</a>
    <span class="social_networks">
        <?php $this->renderPartial('//layouts/addthis', ['force' => true]) ?>
    </span>
    </div>
</div>