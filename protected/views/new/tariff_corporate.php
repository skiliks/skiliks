
<h1 class="page-header"><?php echo Yii::t('site', 'Profile') ?></h1>

<div class="container-3 block-border border-primary bg-transparnt">
<div class="border-primary bg-yellow standard-left-box"><?php $this->renderPartial('//new/_menu_corporate', ['active' => ['tariff' => true]]) ?></div>

    <div class="border-primary bg-light-blue standard-right-box">
        <div class="pad-large profile-tarif-form profilelabel-wrap profile-min-height">
            <div class="row">
                <?php if (null === Yii::app()->user->data()->getAccount()->tariff) : ?>
                    <label class="grid-cell font-large">Тарифный план</label>
                    <div class="grid-cell ">не выбран</div>
                <?php else : ?>
                    <label class="grid-cell font-large">Выбран тарифный план</label>
                    <div class="grid-cell value">
                        <strong class="font-green font-xxlarge"><?php echo strtolower(Yii::app()->user->data()->getAccount()->getTariffLabel()) ?></strong>
                        <small class="font-small font-grey"><?php echo Yii::app()->user->data()->getAccount()->tariff->getFormattedPrice() ?></small>
                    </div>
                <?php endif ?>
                <?php /*
                    <div class="action">
                        <a href="/static/tariffs/ru" class="blue-btn">Сменить</a>
                    </div>
                */ ?>
            </div>

            <div class="row">
                <label class="grid-cell font-large">Действителен до</label>
                <div class="grid-cell">
                    <?php if (null === Yii::app()->user->data()->getAccount()->tariff) : ?>
                        не указано
                    <?php else : ?>
                    <strong class="font-green font-xxlarge"><?php echo date('d.m.Y', strtotime(Yii::app()->user->data()->getAccount()->tariff_expired_at)) ?></strong>
                    <?php endif ?>
                </div>
                <div class="grid-cell">
                    <a class="btn btn-primary" href="/payment/order/<?= Yii::app()->user->data()->getAccount()->tariff->slug ?>">Продлить</a>
                </div>
            </div>

            <div class="row">
                <label class="grid-cell font-large">Доступно симуляций</label>
                <div class="grid-cell">
                    <strong class="font-green font-xxlarge"><?php echo Yii::app()->user->data()->getAccount()->getTotalAvailableInvitesLimit() ?></strong>
                    <small class="font-small font-grey">

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
</div>