<!-- profile-menu -->
<ul class="profile-menu pull-left rounded-list">
    <li class="<?php if (isset($active['personal-data'])){ echo 'active'; }?>">
        <a href="/profile/corporate/personal-data">
            Личные данные
        </a>
    </li>

    <li class="<?php if (isset($active['password'])){ echo 'active'; } ?>">
        <a href="/profile/corporate/password">
            Пароль
        </a>
    </li>

    <li class="<?php if (isset($active['company-info'])){ echo 'active'; }?>">
        <a href="/profile/corporate/company-info">
            Информация о компании
        </a>
    </li>

    <li class="<?php if (isset($active['vacancies'])){ echo 'active'; }?>">
        <a href="/profile/corporate/vacancies">
            Позиции
        </a>
    </li>

    <li class="<?php if (isset($active['referrals'])){ echo 'active'; } ?>">
        <a href="/profile/corporate/referrals">
            Бонусы
        </a>
    </li>

    <!--
    <li class="mnotactive <?php if (isset($active['payment-method'])){ echo 'active'; }?>">
        <a href="/profile/corporate/payment-method">
           Способ оплаты
        </a>
    </li>
    -->

</ul>