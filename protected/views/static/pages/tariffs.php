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
                <div class="subscribe-ti-tariff"><a class="light-btn feedback" href="#">Выбрать</a></div>
            </div>
        </div>
    </div>
<?php endforeach ?>
    <p class="text-right text16"><sup>*</sup> <strong>Свяжитесь с нами,</strong> чтобы приобрести</p>
    <div class="contwrap"><a href="#" class="light-btn feedback">Обратная связь</a>
    <span class="social_networks">

        <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
            <a class="addthis_button_vk"></a>
            <a class="addthis_button_facebook"></a>
            <a class="addthis_button_twitter"></a>
            <a class="addthis_button_google_plusone"  g:plusone:count="false"></a>
            <a class="addthis_button_linkedin"></a>
        </div>
<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5158c9c22198d938"></script>
<!-- AddThis Button END -->

    </span>
    </div>
</div>

<div style="height: 100px; width: 100px;">

