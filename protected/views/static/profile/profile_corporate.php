
<h1><?php echo Yii::t('site', 'Profile') ?></h1>

<div id="profile-tabs" class="tabs-vertical">
    <ul>
        <li><a href="#tab-private-data">Личные данные</a></li>
        <li><a href="#tab-password">Пароль</a></li>
        <li><a href="#tab-company-info">Информация о компании</a></li>
        <li><a href="#tab-vacancies">Вакансии</a></li>
        <li><a href="#tab-tarif">Тариф</a></li>
        <li><a href="#tab-payment-method">Метод оплаты</a></li>
    </ul>
    <div id="tab-private-data">
        <p>tab-private-data</p>
    </div>
    <div id="tab-password">
        <p>tab-password</p>
    </div>
    <div id="tab-company-info">
        <p>company-info</p>
    </div>
    <div id="tab-vacancies">
        <p>vacancies</p>
    </div>
    <div id="tab-tarif">
        <p>tarif</p>
    </div>
    <div id="tab-payment-method">
        <p>payment-method</p>
    </div>
</div>

<script>
    $(function() {
        $( "#profile-tabs" ).tabs();
        $( "#profile-tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#profile-tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
    });
</script>