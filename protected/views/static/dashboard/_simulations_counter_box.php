<div class="counter">
    <strong>Осталось симуляций</strong>
    <span class="label background-blue selenium-simulations-amount">
        <?php echo Yii::app()->user->data()->getAccount()->getTotalAvailableInvitesLimit()?>
    </span>
</div>

<div class="counter">
    <strong>Тарифный план</strong>
    <span class="label background-blue selenium-tariff-name">
        <?php echo Yii::app()->user->data()->getAccount()->getTariffLabel() ?>
    </span>
</div>
