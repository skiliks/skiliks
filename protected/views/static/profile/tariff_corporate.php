
<h1><?php echo Yii::t('site', 'Profile') ?></h1>

<?php $this->renderPartial('_menu_corporate', ['active' => ['tariff' => true]]) ?>

<div class="row">
    <?php if (null === Yii::app()->user->data()->getAccount()->tariff) : ?>
        <label>Тарифный план</label>
        <div class="value">не выбран</div>
    <?php else : ?>
        <label>Выбран тарифный план</label>
        <div class="value">
            <?php echo Yii::app()->user->data()->getAccount()->tariff->label ?>
            <br/>
            <small class="tarifprice"><?php echo Yii::app()->user->data()->getAccount()->tariff->getFormattedPrice() ?></small>
        </div>
    <?php endif ?>
    <div class="action">
        <a href="/static/tariffs/ru">Сменить</a>
    </div>
</div>

<br/>
<br/>

<div class="row">
    <label>Действителен до</label>
    <div class="value">
        <?php if (null === Yii::app()->user->data()->getAccount()->tariff) : ?>
            не указано
        <?php else : ?>
            <?php echo date('d M, Y', strtotime(Yii::app()->user->data()->getAccount()->tariff_expired_at)) ?>
        <?php endif ?>
    </div>
    <div class="action">
        <a href="/static/contacts/ru">Продлить</a>
    </div>
</div>

<br/>
<br/>

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

<br/>
<br/>