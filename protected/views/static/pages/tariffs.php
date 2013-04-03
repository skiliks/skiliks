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
                <div class="brightblock"><?php echo $tariff->getFormattedSafeAmount() ?></div>
                <div class="simulations-amount lightblock"><?php echo $tariff->simulations_amount ?> симуляций *</div>
                <div class="benefits">
                    <?php foreach (explode(',', $tariff->benefits) as $benefit) : ?>
                        <p><?php echo $benefit?></p>
                    <?php endforeach ?>
                </div>
                <div class="subscribe-ti-tariff"><a href="/static/contacts/ru" class="light-btn">Выбрать</a></div>
            </div>
        </div>
    </div>
<?php endforeach ?>
    <p class="text-right text16"><sup>*</sup> <strong>Свяжитесь с нами,</strong> чтобы приобрести</p>
    <div class="contwrap"><a href="#" class="light-btn feedback">Обратная связь</a>
        <span class="socicons"><a href="#" class="iconfb"></a><a href="#" class="icontw"></a>
            <a href="#" class="icongplus"></a><a href="#" class="iconin"></a></span>
    </div>
</div>

<div style="height: 100px; width: 100px;">

