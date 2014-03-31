<?php
    $priceLite = Price::model()->findByAttributes(['alias' => Price::ALIAS_LITE]);
    $priceStarted = Price::model()->findByAttributes(['alias' => Price::ALIAS_STARTED]);
    $priceProfessional = Price::model()->findByAttributes(['alias' => Price::ALIAS_PROFESSIONAL]);
    $priceBusiness = Price::model()->findByAttributes(['alias' => Price::ALIAS_BUSINESS]);
?>

<section>
    <h1 class="pull-content-center">
        <?= 'Скидка 50% на весь 2014 год при оплате до 31 марта' ?>
    </h1>

    <br/>

    <span class="pull-content-center column-full">
        <span class="nice-border background-yellow border-radius-standard us-price-box">
            <div class="pull-content-center us-price-title color-ffffff">
                Lite
            </div>
            <div>
                <span>
                    <img src="">
                </span>
                <span>
                    <?php
                    $counter = 3;
                    $priceLiteThousands = ('ru' == Yii::app()->language)
                        ? floor($priceLite->in_RUB*$counter/1000)
                        : floor($priceLite->in_USD*$counter/1000)
                    ?>
                    <?php //= $priceLiteThousands ?>
                </span>
                <span>
                    <?= ((('ru' == Yii::app()->language)
                        ? $priceLite->in_RUB*$counter
                        : $priceLite->in_USD*$counter) - $priceLiteThousands*1000)
                    ?>
                </span>
            </div>
            <span>

            </span>
            <span>
                <?= ('ru' == Yii::app()->language) ? '10 симуляций' : '10 simulations' ?>
            </span>
            <br/>
            <br/>
            <br/>
            <br/>
            <a class="button-white button-white-hover inter-active label icon-arrow-blue" href="/payment/order">
                <?= 'Купить' ?>
            </a>
        </span>

        <span class="nice-border background-yellow border-radius-standard us-price-box margin-left-18">
            <div class="pull-content-center us-price-title color-ffffff">
                Started
            </div>
            <div>
                <span>
                    <img src="">
                </span>
                <span>
                    <?php
                    $counter = 10;
                    $priceStartedThousands = ('ru' == Yii::app()->language)
                        ? floor($priceStarted->in_RUB*$counter/1000)
                        : floor($priceStarted->in_USD*$counter/1000)
                    ?>
                    <?php //= $priceStartedThousands ?>
                </span>
                <span>
                    <?= ((('ru' == Yii::app()->language)
                        ? $priceStarted->in_RUB*$counter
                        : $priceStarted->in_USD*$counter) - $priceStartedThousands*1000)
                    ?>
                </span>
            </div>
            <span>
                <span>
                    <?= ('ru' == Yii::app()->language) ? 'Экономия 5000 р' : 'Save $300' ?>
                </span>
            </span>
            <span>
                <?= ('ru' == Yii::app()->language) ? '20 симуляций' : '20 simulations' ?>
            </span>
            <br/>
            <br/>
            <br/>
            <br/>
            <a class="button-white button-white-hover inter-active label icon-arrow-blue" href="/payment/order">
                <?= 'Купить' ?>
            </a>
        </span>

        <span class="nice-border background-yellow border-radius-standard us-price-box margin-left-18">
            <div class="pull-content-center us-price-title color-ffffff">
                Professional
            </div>
            <div>
                <span>
                    <img src="">
                </span>
                <span>
                    <?php
                    $counter = 20;
                    $priceProfessionalThousands =
                        ('ru' == Yii::app()->language)
                            ? floor($priceProfessional->in_RUB*$counter/1000)
                            : floor($priceProfessional->in_USD*$counter/1000)
                    ?>
                    <?= $priceProfessionalThousands ?>
                </span>
                <span>
                    <?= ((('ru' == Yii::app()->language)
                        ? $priceProfessional->in_RUB*$counter
                        : $priceProfessional->in_USD*$counter) - $priceProfessionalThousands*1000)
                    ?>
                </span>
            </div>
            <span>
                <span>
                    <?= ('ru' == Yii::app()->language) ? 'Экономия 15000 р' : 'Save $800' ?>
                </span>
            </span>
            <span>
                <?= ('ru' == Yii::app()->language) ? '50 симуляций' : '50 simulations' ?>
            </span>
            <br/>
            <br/>
            <br/>
            <br/>
            <a class="button-white button-white-hover inter-active label icon-arrow-blue" href="/payment/order">
                <?= 'Купить' ?>
            </a>
        </span>

        <span class="nice-border background-yellow border-radius-standard us-price-box margin-left-18">
            <div class="pull-content-center us-price-title color-ffffff">
                Business
            </div>
            <div>
                <span>
                    <img src="">
                </span>
                <span>
                    <?php
                    $counter = 50;
                    $priceBusinessThousands = ('ru' == Yii::app()->language)
                        ? floor($priceBusiness->in_RUB*$counter/1000)
                        : floor($priceBusiness->in_USD*$counter/1000)
                    ?>
                    <?= $priceBusinessThousands ?>
                </span>
                <span>
                    <?= ((('ru' == Yii::app()->language)
                        ? $priceBusiness->in_RUB*$counter
                        : $priceBusiness->in_USD*$counter) - $priceBusinessThousands*1000)
                    ?>
                </span>
            </div>
            <span>
                <span>
                    <?= ('ru' == Yii::app()->language) ? 'Экономия 50000 р' : 'Save $2500' ?>
                </span>
            </span>
            <br/>
            <br/>
            <br/>
            <br/>
            <a class="button-white button-white-hover inter-active label icon-arrow-blue" href="/payment/order">
                <?= 'Купить' ?>
            </a>
        </span>
    </span>

    <div class="us-feedback-margin">
        <br/>
        <br/>

        <a class="button-white button-white-hover inter-active label icon-arrow-blue action-feedback"><?= Yii::t('site', 'Send feedback') ?></a>
        <span class="social_networks">
            <?php // $this->renderPartial('//global_partials/addthis', ['force' => true]) ?>
        </span>
    </div>
</section>

<div class="clearfix"></div>