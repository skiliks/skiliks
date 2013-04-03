<h2 class="thetitle text-center">Тарифные планы: цена подписки в месяц</h2>

<div>
<?php foreach ($tariffs as $tariff): ?>
    <div class="nice-border onetariff">
        <div class="tariff-box radiusthree">
            <label class="tarifname"><?php echo $tariff->label ?></label>
            <div class="price">
                <?php if (round($tariff->price / 1000)): ?>
                    <?php echo round($tariff->price / 1000) ?> т
                <?php endif ?>
                <?php echo $tariff->price % 1000 ?></div>
        </div>
            <div class="tarifwrap">
                <div class="simulations-amount lightblock"><?php echo $tariff->simulations_amount ?> симуляций</div>
                <div class="benefits">
                    <?php foreach (explode(',', $tariff->benefits) as $benefit) : ?>
                        <?php echo $benefit?> <br/><br/>
                    <?php endforeach ?>
                </div>
                <div class="subscribe-ti-tariff"><a href="/static/contacts/ru" class="light-btn">Подписаться</a></div>
            </div>
        </div>
    </div>
<?php endforeach ?>

</div>
<div style="height: 100px; width: 100px;">

