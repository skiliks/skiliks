<div class="downgrade-tariff-popup" style="display: none;">
    <div class="more-side-pads">
        <h2 class="title">Внимание!</h2>
        <ul class="list-ordered">
            <p class="font-xxlarge">
                Вы выбрали тарифный план <span class="tariff_label">Lite</span>
                (<span class="tariff_limits">x<?php /* Заменяется потом с помощью JS */ ?></span> симуляций в месяц).<br/>
                Изменения вступят в силу с <span class="tariff_start">20.12.2013<?php /* Заменяется потом с помощью JS */ ?><</span>.
                <?php if(0 < $account->getTotalAvailableInvitesLimit()): ?>
                    До <span class="tariff_start">х<?php /* Заменяется потом с помощью JS */ ?><</span>
                    у вас остались доступные симуляции
                    (<span class="invite_limits">x<?php /* Заменяется потом с помощью JS */ ?><</span>шт.).</br>
                <?php endif; ?>
            </p>
        </ul>
        <div class="text-center">
            <a href="#" data-href="#" class="bigbtnsubmt tariff-link">Сменить сейчас</a>
            <a href="#" data-href="#" data-class="downgrade-tariff-popup" class="bigbtnsubmt subscribe-ti-tariff-close">Отмена</a>
        </div>
    </div>
</div>

