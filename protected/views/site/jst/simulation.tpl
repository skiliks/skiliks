<script type="text/template" id="icon_panel">
    <ul class="icons-panel">
        <li class="messenger only-active"><a href="#"></a></li>
        <li class="plan"><a href="" id="icons_todo"></a></li>
        <li class="phone"><a href="" id="icons_phone"></a></li>
        <li class="mail"><a href="" id="icons_email"></a></li>
        <li class="door only-active"><a href="" id="icons_visit"></a></li>
        <li class="documents"><a href="" id="icons_documents"></a></li>
    </ul>
</script>

<script type="text/template" id="debug_panel">
    <div class="row">
        <div class="span2">            
                <fieldset>
                    <br/>
                    <h4>Действия</h4>
                    <br/>
                    <form class="simulation-buttons">
                        <input class="btn btn-simulation-stop" type="button" value="SIM стоп">
                    </form>
                    <button class="btn btn-toggle-dialods-sound" type="button" value="" 
                       title="Убрать звук в диалогах/вернуть звук в диалогах">
                        <i class="icon-volume-off"></i>
                    </button>                 
                </fieldset>            
        </div>
        <div class="span3">
            <form class="form-inline trigger-event">
                <fieldset>
                    <br/>
                    <h4>Запуск события</h4>
                    <br/>
                    <div class="control-group">
                        <label for="addTriggerSelect" class="control-label">Код события:</label>
                        <input name="code" id="addTriggerSelect" required="required" type="text" class="span1">
                        
                    </div>
                    <div class="control-group">
                        <label for="addTriggerDelay" class="control-label">Задержка(игровые
                            минуты):</label>

                        <div class="controls">
                            <input name="delay" type="number" id="addTriggerDelay"  required="required" class="span1" value="0">
                            <input type="submit" value="Создать" class="btn btn-primary">
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls">
                            <label for="addTriggerClearEvents" class="checkbox">
                                <input name="clear_events" value="0" id="addTriggerClearEvents" type="checkbox"/>
                                Очистить очередь событий
                            </label>
                            <label for="addTriggerClearAssessment" class="checkbox">
                                <input name="clear_assessment" value="0" id="addTriggerClearAssessment" type="checkbox"/>
                                Очистить очередь оценки
                            </label>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="span4">
            <form class="form-inline form-set-time">
                <fieldset>
                    <br/>
                    <h4>Установка времени</h4>
                    <br/>
                    <div class="control-group">
                        <label for="setTimeHours" class="control-label">Новое время:</label>

                        <div class="controls">
                            <input name="hours" id="setTimeHours" type="number" class="span1" maxlength="2"/>
                            <span> : </span>
                            <input name="minutes" type="number" class="span1" maxlength="2">
                            <input type="submit" value="Задать" class="btn btn-primary">
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn set-time" data-hour="0" data-minute="0">0:00</button>
                        <button class="btn set-time" data-hour="10" data-minute="0">10:00</button>
                        <button class="btn set-time" data-hour="11" data-minute="0">11:00</button>
                        <button class="btn set-time" data-hour="12" data-minute="0">12:00</button>
                        <button class="btn set-time" data-hour="13" data-minute="0">13:00</button>
                        <button class="btn set-time" data-hour="14" data-minute="0">14:00</button>
                        <button class="btn set-time" data-hour="15" data-minute="0">15:00</button>
                        <button class="btn set-time" data-hour="16" data-minute="0">16:00</button>
                        <button class="btn set-time" data-hour="17" data-minute="0">17:00</button>
                        <button class="btn set-time" data-hour="17" data-minute="50">17:50</button>
                    </div>
                </fieldset>
            </form>
        </div>
        <div class="span3">
            <form class="form-inline form-flags">
                <fieldset>
                    <br/>
                    <h4>Текущее значение флагов<span class="current-time"></span></h4>
                    <br/>
                    <table class="table table-bordered" >
                        <thead></thead>
                        <tbody>
                            <tr>
                                <td>
                                    Неизвестно.
                                </td>
                            </tr>
                        <tbody>
                    </table>
                </fieldset>
            </form>
        </div>
    </div>
</script>

<script type="text/template" id="simulation_template">
    <div style="width: 100%;height: 100%;">
        <div id="canvas" class="canvas">
            <ul class="main-screen-stat">
                <li class="time"><span class="hour"></span><span class="delimiter">:</span><span
                        class="minute"></span></li>
                <li><img src="{$assetsUrl}/img/main-screen/icon-bat-full.png" alt=""/></li>
                <li><a><img alt="" src="{$assetsUrl}/img/main-screen/icon-help.png"></a></li>
            </ul>
            <div class="phone-dialog-div"></div>
            <div class="visitor-container"></div>
            <div class="plan-container"></div>
            <nav class="main-screen-icons"></nav>
        </div>
        <div class="simulation-controls">
            <div class="debug-panel"></div>
        </div>

    </div>
    <div id="location" class="location"></div>
    </div>
    <div id="message" class="message"></div>
</script>
