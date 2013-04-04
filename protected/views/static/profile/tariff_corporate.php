
<h2 class="thetitle"><?php echo Yii::t('site', 'Profile') ?></h2>

<div class="transparent-boder profilewrap">
<?php $this->renderPartial('_menu_corporate', ['active' => ['tariff' => true]]) ?>

    <div class="form profileform tarifform">
        <div class="row">
            <?php if (null === Yii::app()->user->data()->getAccount()->tariff) : ?>
                <label>Тарифный план</label>
                <div class="value">не выбран</div>
            <?php else : ?>
                <label>Выбран тарифный план</label>
                <div class="value">
                    <?php echo strtolower(Yii::app()->user->data()->getAccount()->getTariffLabel()) ?>
                    <br/>
                    <small class="tarifprice"><?php echo Yii::app()->user->data()->getAccount()->tariff->getFormattedPrice() ?></small>
                </div>
            <?php endif ?>
            <div class="action">
                <a href="/static/tariffs/ru" class="blue-btn">Сменить</a>
            </div>
        </div>

        <div class="row rowpad30">
            <label>Действителен до</label>
            <div class="value">
                <?php if (null === Yii::app()->user->data()->getAccount()->tariff) : ?>
                    не указано
                <?php else : ?>
                    <?php echo date('d.m.Y', strtotime(Yii::app()->user->data()->getAccount()->tariff_expired_at)) ?>
                <?php endif ?>
            </div>
            <div class="action">
                <a class="light-btn lightbox-30934004754349" href="#">Продлить</a>
            </div>
        </div>

        <div class="row">
            <label>Доступно симуляций</label>
            <div class="value">
                <span class="simulations-counter"><?php echo Yii::app()->user->data()->getAccount()->invites_limit ?></span><br/>
                <small class="expire-date">

                    <?php if (null === Yii::app()->user->data()->getAccount()->tariff) : ?>
                        Без ограничения по времени
                    <?php else : ?>
                        До <?php echo date('d M, Y', strtotime(Yii::app()->user->data()->getAccount()->tariff_expired_at)) ?>
                    <?php endif ?>
                </small>
            </div>
        </div>
    </div>
</div>