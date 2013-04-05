<h2 class="thetitle text-center">Тарифные планы: цена подписки в месяц</h2>

<div class="tarifswrap">
<?php foreach ($tariffs as $tariff): ?>
    <div class="nice-border onetariff">
        <div class="tariff-box radiusthree">
            <label class="tarifname"><?php echo $tariff->label ?></label>
            <div class="price">
                <p><?php if (round($tariff->price / 1000)): ?>
                    <span><?php echo round($tariff->price / 1000) ?></span>
                <?php endif ?>
                <?php echo $tariff->price % 1000 ?></p>
            </div>
            <div class="tarifwrap">
                <div class="brightblock">
                    <?php if ($tariff->safe_amount !== '0.00'): ?><!-- $tariff->is_free ||  -->
                        Экономия <?php echo $tariff->getFormattedSafeAmount() ?> р
                    <?php else: ?>
                        бесплатно
                    <?php endif ?>
                </div>
                <div class="simulations-amount lightblock"><?php echo $tariff->getFormattedSimulationsAmount() ?>
                    <?php if ($tariff->safe_amount !== '0.00'): ?>*<?php endif ?></div>
                <div class="benefits">
                    <?php foreach (explode(',', $tariff->benefits) as $benefit) : ?>
                        <p><?php echo $benefit?></p>
                    <?php endforeach ?>
                </div>
                <?php if($tariff->id !== $user->getAccount()->tariff_id){ ?>
                <div class="subscribe-ti-tariff"><a class="light-btn feedback" href="<?php if($tariff->label === "Lite"){ echo "/tariffs/lite"; }else{ echo "#"; } ?>">Выбрать</a></div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php endforeach ?>
    <p class="text-right text16"><sup>*</sup> <strong>Свяжитесь с нами,</strong> чтобы приобрести</p>
    <div class="contwrap"><a href="#" class="light-btn feedback">Обратная связь</a>
    <span class="social_networks">
        <?php $this->renderPartial('//layouts/addthis') ?>
    </span>
    </div>
</div>

<div style="height: 100px; width: 100px;">

