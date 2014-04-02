<?php
    $priceLite = Price::model()->findByAttributes(['alias' => Price::ALIAS_LITE]);
    $priceStarted = Price::model()->findByAttributes(['alias' => Price::ALIAS_STARTED]);
    $priceProfessional = Price::model()->findByAttributes(['alias' => Price::ALIAS_PROFESSIONAL]);
    $priceBusiness = Price::model()->findByAttributes(['alias' => Price::ALIAS_BUSINESS]);

    $currencyUrl = ('ru' == Yii::app()->language) ? 'rub.png' : 'usd.png' ;
?>

<section>
    <h1 class="pull-content-center">
        <?= Yii::t('site', 'Tariff plans 2014') ?>
    </h1>

    <br/>

    <span class="pull-content-center column-full">
        <span class="nice-border background-yellow border-radius-standard us-tariff-box color-ffffff ">
            <div class="pull-content-center us-price-title color-ffffff">
                Lite
            </div>
            <div class="us-price-box">
                <span class="us-icon-currency"
                    style="margin-right: <?= ('ru' == Yii::app()->language) ? '-5px' : '0'; ?>">
                    <img src="<?= $this->assetsUrl?>/img/site/1280/tariff/<?= $currencyUrl ?>">
                </span>
                <span class="us-price-thousands color-ffffff">
                    <?php
                    $counter = 3;
                    $priceLiteThousands = ('ru' == Yii::app()->language)
                        ? floor($priceLite->in_RUB*$counter/1000)
                        : floor($priceLite->in_USD*$counter/1000)
                    ?>
                    <?php echo (1 <= $priceLiteThousands) ? $priceLiteThousands : ' '; ?>
                </span>
                <span class="us-price-hundreds color-ffffff"
                    style="<?php echo (1 <= $priceLiteThousands) ? '' : 'padding-top: 8px;' ?>">
                    <?= ((('ru' == Yii::app()->language)
                        ? $priceLite->in_RUB*$counter
                        : $priceLite->in_USD*$counter) - $priceLiteThousands*1000)
                    ?>
                </span>
            </div>
            <br/>
            <span class="us-safe-amount pull-center" style="opacity: 0">
                &nbsp;
            </span>
            <br/>
            <span class="us-amount color-3D4041 pull-center">
                <?= ('ru' == Yii::app()->language) ? '3 симуляции' : '3 simulations' ?>
            </span>
            <br/>
            <a href="static/product-diagnostic" class="us-description color-3D4041 pull-center"
                style="opacity: 0">&nbsp;</a>
            <br/>
            <a class="button-white button-white-XL button-white-hover inter-active label icon-arrow-blue"
               href="/payment/order?ordered=3">
                <?= 'Купить' ?>
            </a>
        </span>

        <span class="nice-border background-yellow border-radius-standard us-tariff-box margin-left-18">
            <div class="pull-content-center us-price-title color-ffffff">
                Started
            </div>
            <div class="us-price-box">
                <span class="us-icon-currency"
                      style="margin-right: <?= ('ru' == Yii::app()->language) ? '-5px' : '0'; ?>">
                    <img src="<?= $this->assetsUrl?>/img/site/1280/tariff/<?= $currencyUrl ?>">
                </span>
                <span class="us-price-thousands color-ffffff">
                    <?php
                    $counter = 10;
                    $priceStartedThousands = ('ru' == Yii::app()->language)
                        ? floor($priceStarted->in_RUB*$counter/1000)
                        : floor($priceStarted->in_USD*$counter/1000)
                    ?>
                    <?php echo (1 <= $priceStartedThousands) ? $priceStartedThousands : ' '; ?>
                </span>
                <span class="us-price-hundreds color-ffffff"
                    style="<?php echo (1 <= $priceStartedThousands) ? '' : 'padding-top: 8px;' ?>">
                    <?= ((('ru' == Yii::app()->language)
                        ? $priceStarted->in_RUB*$counter
                        : $priceStarted->in_USD*$counter) - $priceStartedThousands*1000)
                    ?>
                </span>
            </div>
            <br/>
            <span  class="us-safe-amount pull-center color-ffffff">
                <?= ('ru' == Yii::app()->language) ? 'Экономия 5000 р' : 'Save $300' ?>
            </span>
            <br/>
            <span class="us-amount color-3D4041 pull-center">
                <?= ('ru' == Yii::app()->language) ? '10 симуляций' : '10 simulations' ?>
            </span>
            <br/>
            <a href="static/product-diagnostic" class="us-description color-3D4041 pull-center"
               style="opacity: 0">&nbsp;</a>
            <br/>
            <a class="button-white button-white-XL button-white-hover inter-active label icon-arrow-blue"
               href="/payment/order?ordered=10">
                <?= 'Купить' ?>
            </a>
        </span>

        <span class="nice-border background-yellow border-radius-standard us-tariff-box margin-left-18">
            <div class="pull-content-center us-price-title color-ffffff">
                Professional
            </div>
            <div class="us-price-box">
                <span class="us-icon-currency"
                      style="margin-right: <?= ('ru' == Yii::app()->language) ? '-5px' : '0'; ?>">
                    <img src="<?= $this->assetsUrl?>/img/site/1280/tariff/<?= $currencyUrl ?>">
                </span>
                <span class="us-price-thousands color-ffffff">
                    <?php
                    $counter = 20;
                    $priceProfessionalThousands =
                        ('ru' == Yii::app()->language)
                            ? floor($priceProfessional->in_RUB*$counter/1000)
                            : floor($priceProfessional->in_USD*$counter/1000)
                    ?>
                    <?= $priceProfessionalThousands ?>
                </span>
                <span class="us-price-hundreds color-ffffff"
                    style="<?php echo (1 <= $priceProfessionalThousands) ? '' : 'padding-top: 8px;' ?>">
                    <?= ((('ru' == Yii::app()->language)
                        ? $priceProfessional->in_RUB*$counter
                        : $priceProfessional->in_USD*$counter) - $priceProfessionalThousands*1000)
                    ?>
                </span>
            </div>
            <br/>
            <span class="us-safe-amount pull-center color-ffffff">
                <?= ('ru' == Yii::app()->language) ? 'Экономия 15000 р' : 'Save $800' ?>
            </span>
            <br/>
            <span class="us-amount color-3D4041 pull-center">
                <?= ('ru' == Yii::app()->language) ? '20 симуляций' : '20 simulations' ?>
            </span>
            <br/>
            <a href="/static/product-diagnostic" class="us-description color-3D4041 pull-center"
                style="opacity: <?= ('ru' == Yii::app()->language) ? 1 : 0 ?>"  >
                <?= ('ru' == Yii::app()->language) ? '+ Диагностика <br/>управленческого <br/>потенциала компании'
                    : '&nbsp;' ?>
            </a>
            <br/>
            <a class="button-white button-white-hover inter-active label icon-arrow-blue"
               href="/payment/order?ordered=20">
                <?= 'Купить' ?>
            </a>
        </span>

        <span class="nice-border background-yellow border-radius-standard us-tariff-box margin-left-18">
            <div class="pull-content-center us-price-title color-ffffff">
                Business
            </div>
            <div class="us-price-box">
                <span class="us-icon-currency"
                    style="margin-right: <?= ('ru' == Yii::app()->language) ? '-5px' : '0'; ?>">
                    <img src="<?= $this->assetsUrl?>/img/site/1280/tariff/<?= $currencyUrl ?>">
                </span>
                <span class="us-price-thousands color-ffffff">
                    <?php
                    $counter = 50;
                    $priceBusinessThousands = ('ru' == Yii::app()->language)
                        ? floor($priceBusiness->in_RUB*$counter/1000)
                        : floor($priceBusiness->in_USD*$counter/1000)
                    ?>
                    <?= $priceBusinessThousands ?>
                </span>
                <span class="us-price-hundreds color-ffffff"
                    style="<?php echo (1 <= $priceBusinessThousands) ? '' : 'padding-top: 8px;' ?>">
                    <?= ((('ru' == Yii::app()->language)
                        ? $priceBusiness->in_RUB*$counter
                        : $priceBusiness->in_USD*$counter) - $priceBusinessThousands*1000)
                    ?>
                </span>
            </div>
            <br/>
            <span class="us-safe-amount pull-center color-ffffff">
                <?= ('ru' == Yii::app()->language) ? 'Экономия 50000 р' : 'Save $2500' ?>
            </span>
            <br/>
            <span class="us-amount color-3D4041 pull-center">
                <?= ('ru' == Yii::app()->language) ? '50 симуляций' : '50 simulations' ?>
            </span>
            <br/>
            <a href="/static/product-diagnostic" class="us-description color-3D4041 pull-center"
                style="opacity: <?= ('ru' == Yii::app()->language) ? 1 : 0 ?>">
                <?= ('ru' == Yii::app()->language) ? '+ Диагностика <br/>управленческого <br/>потенциала компании'
                    : '&nbsp;' ?>
            </a>
            <br/>
            <a class="button-white button-white-hover inter-active label icon-arrow-blue"
               href="/payment/order?ordered=50">
                <?= 'Купить' ?>
            </a>
        </span>
    </span>

    <div class="us-feedback-margin">
        <?php if ('ru' == Yii::app()->language) : ?>
            <br/>
            <span class="pull-content-left color-3D4041" style="margin-bottom: 10px; font-size: 1.15em;">
                При покупке любого тарифного пакета Вы можете приобрести дополнительные симуляции поштучно.<br/>
                Цена 1 штуки рассчитывается исходя из выбранного тарифного плана.
            </span>
        <?php endif; ?>
        <br/>

        <a class="button-white button-white-hover inter-active label icon-arrow-blue action-feedback reset-margin">
            <?= Yii::t('site', 'Send feedback') ?>
        </a>

        <span class="social_networks">
            <?php // $this->renderPartial('//global_partials/addthis', ['force' => true]) ?>
        </span>
    </div>
</section>

<div class="clearfix"></div>