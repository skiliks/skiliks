<?php /* @var $user YumUser */ ?>
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">
<?php $this->renderPartial('_menu_corporate', ['active' => ['tariff' => true]]) ?>

    <div class="form profileform tarifform">
        <div class="row">
            <?php if (null === $user->getAccount()->tariff) : ?>
                <label>Тарифный план</label>
                <div class="value">не выбран</div>
            <?php else : ?>
                <label>Выбран тарифный план</label>
                <div class="value">
                    <?php echo strtolower($user->getAccount()->getTariffLabel()) ?>
                    <br/>
                    <small class="tarifprice"><?php echo $user->getAccount()->tariff->getFormattedPrice() ?> руб. </small>
                </div>
            <?php endif ?>

        </div>

        <div class="row rowpad30">
            <label>Действителен до</label>
            <div class="value">
                <?php if (null === $user->getAccount()->tariff) : ?>
                    не указано
                <?php else : ?>
                    <?php echo date('d.m.Y', strtotime($user->getAccount()->tariff_expired_at)) ?>
                <?php endif ?>
            </div>
            <?php if($user->account_corporate->getActiveTariff()->isDisplayOnTariffsPage()) : ?>
                <div class="action">
                    <a class="light-btn make-order-button" href="/payment/order/<?= $user->getAccount()->tariff->slug ?>">Продлить</a>
                </div>
            <?php else : ?>
                <div class="action">
                    <a class="light-btn make-order-button" href="/static/tariffs/">Сменить</a>
                </div>
            <?php endif ?>
        </div>

        <div class="row">
            <label>Доступно симуляций</label>
            <div class="value">
                <span class="simulations-counter"><?php echo $user->getAccount()->getTotalAvailableInvitesLimit() ?></span><br/>
                <small class="expire-date">

                    <?php if (null === $user->getAccount()->tariff) : ?>
                        Без ограничения по времени
                    <?php else : ?>
                        До <?php echo date('d', strtotime($user->getAccount()->tariff_expired_at)), " ",
                            Yii::t('site', date('M', strtotime($user->getAccount()->tariff_expired_at))), " ",
                            date('Y', strtotime($user->getAccount()->tariff_expired_at));
                        ?>
                    <?php endif ?>
                </small>
            </div>
        </div>
    </div>
</div>