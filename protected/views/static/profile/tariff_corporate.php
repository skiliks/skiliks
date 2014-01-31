
<section class="page-title-box column-full pull-content-left ">
    <h1 class="bottom-margin-standard"><?php echo Yii::t('site', 'Profile') ?></h1>
</section>

<section class="pull-content-left nice-border reset-padding
    border-radius-standard background-transparent-20 unstandard-content-box-height">

    <!--div class="transparent-boder profilewrap"-->
    <aside class="column-1-3 inline-block background-yellow border-radius-standard vertical-align-top">
        <?php $this->renderPartial('_menu_corporate', ['active' => ['tariff' => true]]) ?>
    </aside>

    <section class="column-2-3-wide inline-block border-radius-standard background-F3FFFF
         pull-right pull-content-left vertical-align-top profile-right-side">

        <div class="data">
            <?php if (null === $user->getAccount()->tariff) : ?>
                <label class="vertical-align-top">Тарифный план</label>
                <span class="unstandard-second-column">
                    <strong>не выбран</strong>
                </span>
            <?php else : ?>
                <label class="vertical-align-top">Выбран тарифный план</label>
                <span class="unstandard-second-column">
                    <strong>
                        <?php echo $user->getAccount()->getTariffLabel() ?>
                    </strong>
                    <small>
                        <?php echo $user->getAccount()->tariff->getFormattedPrice(Yii::app()->getLanguage()) ?> руб.
                    </small>
                </span>
            <?php endif ?>

            <?php if($user->account_corporate->getActiveTariff()->isDisplayOnTariffsPage()) : ?>
                <a class="button-white inter-active label icon-arrow-blue vertical-align-top" href="/static/tariffs">
                    Сменить
                </a>
            <?php endif ?>
        </div>

        <div class="data row">
            <label class="vertical-align-top">Действителен до</label>
            <span class="unstandard-second-column">
                <strong>
                    <?php if (null === $user->getAccount()->tariff) : ?>
                        не указано
                    <?php else : ?>
                        <?php echo date('d', strtotime($user->getAccount()->tariff_expired_at)) ?>
                        <?php echo Yii::t('site', date('M', strtotime($user->getAccount()->tariff_expired_at))) ?>.
                        <?php echo date('Y', strtotime($user->getAccount()->tariff_expired_at)) ?>
                    <?php endif ?>
                </strong>
            </span>

            <?php if($user->account_corporate->getActiveTariff()->isDisplayOnTariffsPage()) : ?>
                 <a class="button-white inter-active label icon-arrow-blue vertical-align-top" href="/payment/order/<?= $user->getAccount()->tariff->slug ?>">
                     Продлить
                 </a>
            <?php else : ?>
                <a class="button-white inter-active label icon-arrow-blue vertical-align-top" href="/static/tariffs">
                    Сменить
                </a>
            <?php endif ?>
        </div>

        <div class="data row">
            <label class="vertical-align-top">Доступно симуляций</label>
            <span class="unstandard-second-column">
                <strong>
                    <?php echo $user->getAccount()->getTotalAvailableInvitesLimit() ?>
                </strong>
                <small>

                    <?php if (null === $user->getAccount()->tariff) : ?>
                        Без ограничения по времени
                    <?php else : ?>
                        До <?php echo date('d', strtotime($user->getAccount()->tariff_expired_at)), " ",
                        Yii::t('site', date('M', strtotime($user->getAccount()->tariff_expired_at))), " ",
                        date('Y', strtotime($user->getAccount()->tariff_expired_at));
                        ?>
                    <?php endif ?>
                </small>
            </span>
        </div>

    </section>
</section>

<div class="clearfix column-full"></div>