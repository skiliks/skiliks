<h2 class="thetitle text-center">Тарифные планы: цена подписки в месяц</h2>

<div>
<?php foreach ($tariffs as $tariff): ?>
    <div class="tariff-box radiusthree nice-border">
        <label><?php echo $tariff->label ?></label>
        <br/>
        <br/>
        <div class="price"><?php echo $tariff->getFormattedPrice() ?></div>
        <br/>
        <br/>
        <div class="simulations-amount"><?php echo $tariff->simulations_amount ?> симуляций</div>
        <br/>
        <br/>
        <div class="benefits">
            <?php foreach (explode(',', $tariff->benefits) as $benefit) : ?>
                <?php echo $benefit?> <br/><br/>
            <?php endforeach ?>
        </div>
        <br/>
        <br/>
        <div class="subscribe-ti-tariff"><a href="/static/contacts/ru">Подписаться</a></div>
    </div>
<?php endforeach ?>
</div>
<div style="height: 100px; width: 100px;">

