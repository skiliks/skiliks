<ul class="side-menu unstyled">
    <li class="side-item <?php if (isset($active['personal-data'])){ echo 'active proxima-bold'; }?>"><a href="/profile/corporate/personal-data">Личные данные</a></li>
    <li class="side-item <?php if (isset($active['password'])){ echo 'active proxima-bold'; } ?>"><a href="/profile/corporate/password">Пароль</a></li>
    <li class="side-item <?php if (isset($active['company-info'])){ echo 'active proxima-bold'; }?>"><a href="/profile/corporate/company-info">Информация о компании</a></li>
    <li class="side-item <?php if (isset($active['vacancies'])){ echo 'active proxima-bold'; }?>"><a href="/profile/corporate/vacancies">Вакансии</a></li>
    <li class="side-item <?php if (isset($active['tariff'])){ echo 'active proxima-bold'; } ?>"><a href="/profile/corporate/tariff">Тариф</a></li>

    <li class="side-item mnotactive <?php if (isset($active['payment-method'])){ echo 'active'; }?>"><a href="javascript:void(0);">Способ оплаты</a></li>
    <!--<a href="/profile/corporate/payment-method">Способ оплаты</a> -->
</ul>