<?php /* @var $account UserAccountCorporate */ ?>
<div class="tariff-replace-now-popup" style="display: none;">
    <div class="more-side-pads">
        <h2 class="title">Внимание!</h2>
        <ul class="list-ordered">
            <p class="font-xxlarge">У вас <?php echo $account->invites_limit ?> доступных симуляций. Если вы хотите перейти на новый тариф прямо сейчас, то эти симуляции сгорят</br>
            </p>
        </ul>
        <div class="text-center">
            <a href="#" data-href="#" class="bigbtnsubmt tariff-link">Сменить сейчас</a>
            <a href="#" data-href="#" data-class="tariff-replace-now-popup" class="bigbtnsubmt subscribe-ti-tariff-close">Сменить позже</a>
        </div>
    </div>
</div>