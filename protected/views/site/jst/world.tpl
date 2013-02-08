<script type="text/template" id="start_simulation_menu">
    <div class="world-index-mainDiv">
        <@ for (var simulation in simulations) { @>
        <input type="button" value="Начать симуляцию <@= simulations[simulation] @>" data-sim-id="<@= simulation @>"
               class="btn simulation-start">
        <br/>
        <br/>
        <@ } @>
        <input type="button" value="Изменить личные данные" class="btn settings">
        <br>
        <br>
        <input type="button" value="Выход" class="btn logout">
    </div>
</script>

<script type="text/template" id="settings_template">
    <div class="world-sett-mainDiv">
        <form action="">
            <div><label for="pass1" class="def-label-200">Пароль<span style="color: red; ">*</span></label><input
                    id="pass1" type="password" class="span3"></div>
            <br>

            <div><label for="pass2" class="def-label-200">Подтверждение пароля<span
                    style="color: red; ">*</span></label><input id="pass2" type="password" class="span3"></div>
            <br>

            <div class="world-sett-b2Div"><input type="submit" value="Изменить пароль" class="btn"></div>
            <br><br>

            <div class="world-sett-b3Div"><input type="button" onclick="world.drawWorld();" value="Вернуться"
                                                 class="btn"></div>
        </form>
    </div>
</script>

<script type="text/template" id="login_template">
    <div class="world-index-mainDiv" style="width: 400px; padding-top: 50px">
        <form action="" class="login-form">

            <div><label for="login">E-mail</label><input id="login" type="text" class="input-large"></div>

            <div><label for="pass">Пароль</label><input id="pass" type="password" class="input-large"></div>

            <div class="form-actions"><input type="submit"
                                             value="Вход" class="btn btn-primary">&nbsp;
                <input type="button" onclick="register.drawDefault();" value="Регистрация" class="btn">
                <input type="button" onclick="register.lostPass();" value="Забыли пароль?"
                       class="btn">
            </div>
        </form>
    </div>
</script>

<!-- suppress HtmlUnknownTag -->

<script type="text/template" id="dialog_template">    
</script>
