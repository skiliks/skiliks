<div class="invites-limit <?php echo (Yii::app()->user->data()->countInvitesToGive() < 10) ? 'small-invites-limit' : ''; ?>">
    <strong class="font-large">Осталось симуляций</strong> <span class="bg-blue font-white small-block proxima-bold"><?php echo Yii::app()->user->data()->getAccount()->invites_limit?></span>
</div>
<div><strong class="font-large">Тарифный план</strong>
    <a class="bg-blue font-white small-block proxima-bold">
        <?php echo Yii::app()->user->data()->getAccount()->getTariffLabel() ?>
    </a>
</div>
<!--a href="/profile/corporate/tariff" class="greenbtn">Управление подпиской</a-->