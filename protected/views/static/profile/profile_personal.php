
<h1><?php echo Yii::t('site', 'Profile') ?></h1>

<div id="profile-tabs" class="tabs-vertical">
    <ul>
        <li><a href="#tab-private-data">Личные данные</a></li>
        <li><a href="#tab-password">Пароль</a></li>
    </ul>
    <div id="tab-private-data">
        <p>tab-private-data</p>
    </div>
    <div id="tab-password">
        <p>tab-password</p>
    </div>
</div>

<script>
    $(function() {
        $( "#profile-tabs" ).tabs();
        $( "#profile-tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#profile-tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
    });
</script>