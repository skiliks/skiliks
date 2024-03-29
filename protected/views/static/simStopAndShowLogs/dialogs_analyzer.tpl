<style>

    .badge-inverse:hover {
        background-color: #FFD324;
    }

    .container {
        width: 1800px;
        margin: 0 auto;
    }

    #portamento_container {
        float:right;
        position:relative;
    }

    #portamento_container #flow-menu {
        float:none;
        position:absolute;
    }

    #portamento_container #flow-menu.fixed {
        position:fixed;
    }

</style>

<div style="margin-left: 170px;">
    <h1> Анализатор диалогов </h1>
    Источник: {$sourceName}.
</div>

<div id="flow-menu-wrapper" class="row" style="overflow: hidden; width: 1500px;">
    <div class="span2" style="width: 180px;">
        <div id="flow-menu" style="padding-right: 0px;">
            <span class="btn toggle-dialogs" style="width: 140px; text-align: left;">
                <i class="icon icon-user pull-left" style="margin: 10px 10px 10px 0;"></i>
                <div class="pull-left">
                Диалоги видны<br/>
                (скрыть)
                </div>
            </span>
            <br/>
            <br/>
            <span class="btn btn-inverse toggle-emails" style="width: 140px; text-align: left;">
                <i class="icon icon-white icon-envelope pull-left" style="margin: 10px 10px 10px 0;"></i>
                <div class="pull-left">
                Письма скрыты<br/>
                (показать)
                </div>
            </span>
            <br/>
            <br/>
            <a href="#time-based-events" class="btn" style="width: 140px;">
                <i class="icon-time"></i>
                Перейти к<br/>
                событиям которые<br/>
                начинаются<br/>
                по времени
            </a>
            <br/>
            <br/>
            <a href="#event-based-events" class="btn" style="width: 140px;">
                <i class="icon-comment"></i>
                Перейти к <br/>
                событиям которые <br/>
                начинаются по вызову<br/>
                из диалога
            </a>
            <br/>
            <br/>
            <a href="#behaviours-list" class="btn" style="width: 140px;">
                <i class="icon-list"></i>
                Перейти к <br/>
                списку поведений<br/>
                и их проявлений
            </a>

            {if ($isDbMode)}
                <br/>
                <br/>
                <span class="btn toggle-behaviour-without-manifests" style="width: 140px; text-align: left;">
                    <div class="pull-left">
                        Поведения<br/>
                        без проявлений<br/>
                        показаны
                        (скрыть)
                    </div>
                </span>
                <br/>
                <br/>
                <span class="btn toggle-behaviour-with-manifests" style="width: 140px; text-align: left;">
                    <div class="pull-left">
                        Поведения<br/>
                        с проявлениями<br/>
                        показаны
                        (скрыть)
                    </div>
                </span>
                <br/>
                <br/>
            {/if}
        </div>
    </div>

    <a name="time-based-events"></a>

    <div class="span10" style="width: 1200px;">
        <table class="table" style="width: 1600px;">
            <thead>
                <tr>
                    <td><h3>События начинающиеся по времени</h3></td>
                </tr>
            </thead>
            <tbody>
                {foreach $analyzer->hoursChainOfEventsStartedByTime as $line}
                    <tr class="" style="background-color: #3e5259; color: #fff;">
                        <td colspan="7">
                            <strong>{$line->title}</strong>
                        </td>
                    </tr>
                    {foreach $line->events as $aEvent}
                        <tr class="row-{$aEvent->cssIcon}" style="{$aEvent->cssRowColor} height: 100px;">
                            <td class="span4">
                                <a name="{$aEvent->event->code}"></a>
                                {$analyzer->getFormattedAEventHeader($aEvent)}
                                <br/>

                                {$i = 1}
                                <table class="table-condensed pull-left" style="margin-top: 0px;">
                                    <tbody>
                                        <tr>
                                        {foreach $aEvent->replicas as $step}
                                            <td class="span1" style="width: 140px;">
                                            <span class="badge">{$i}</span> <br/>
                                            <!-- step No -->
                                            {foreach $step as $replica}
                                                <span class="badge badge-inverse" title="{$replica->text}">
                                                    {$replica->replica_number}</span>
                                                    <!-- replica No -->
                                                    <a href="#{$replica->next_event_code}"
                                                       title="{$analyzer->getEventTitleByCode($replica->next_event_code)}">
                                                        {$replica->next_event_code}</a>
                                                        {$analyzer->getFormattedReplicaFlag($replica)}
                                                <br/>
                                                <!-- flag block replica -->
                                                {if (isset($analyzer->flagsBlockReplica[$replica->id])) }
                                                    {$replica->replica_number} Need:
                                                    {foreach $analyzer->flagsBlockReplica[$replica->id] as $flagBlock}
                                                        {$flagBlock->flag_code}<br/>
                                                    {/foreach}
                                                {/if}
                                            {/foreach}
                                            </td>
                                            {$i = $i + 1}
                                        {/foreach}
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        {if (isset($analyzer->tree[$aEvent->event->code]))}
                            {foreach $analyzer->tree[$aEvent->event->code] as $branch}
                                <tr class="{$analyzer->getCssSaveEventCode($aEvent)}-variations variations">
                                    <td>
                                    {$i = 0}
                                    {foreach $branch as $element}
                                        <div class="pull-left" style="width: 135px; border-right: 1px solid #ddd; padding-left: 7px;">
                                            <a href="#{$element['prevCode']}"
                                               title="{$analyzer->getReplicaHintByCodeStepReplicaNumber($element['prevCode'],$element['step'],$element['replica'])}">
                                                {$element['prevCode']}[{$element['step']}-{$element['replica']}]</a>
                                                <br/>
                                                {$element['startTime']}
                                                <br/>
                                                {if (isset($element['flagToSwitch']) && null !== $element['flagToSwitch'])}
                                                    Switch: <span class="label label-info">{$element['flagToSwitch']}&rarr;1</span>
                                                {/if}
                                                <br/>
                                                {if (isset($element['flagsToBlockHtml']) && '' != $element['flagsToBlockHtml'])}
                                                    Need: {$element['flagsToBlockHtml']}
                                                {/if}
                                        </div>

                                        <!-- Показать результируюшее событие -->
                                        {$i = $i + 1}
                                        {if ($i == count($branch))}
                                            <div class="pull-left" style="width: 135px;  padding-left: 7px;">
                                                {$element['code']}
                                                <br/>
                                                {$element['startTime']}
                                                <br/>
                                                {if (null !== $element['flagToSwitch'])}
                                                    Switch: <span class="label label-info">{$element['flagToSwitch']}&rarr;1</span>
                                                {/if}
                                                <br/>
                                                {if (isset($element['flagsToBlockHtml']) && '' != $element['flagsToBlockHtml'])}
                                                    Need: {$element['flagsToBlockHtml']}
                                                {/if}
                                            </div>
                                        {/if}
                                    {/foreach}
                                    </td>
                                </tr>
                            {/foreach}
                        {/if}
                    {/foreach}
                {/foreach}
            </tbody>
        </table>

        <br/>

        <a name="event-based-events"></a>

        <table class="table" style="width: 1400px;">
            <thead>
            <tr>
                <td><h3>События начинающиеся по вызову из диалога</h3></td>
            </tr>
            </thead>
        {foreach $analyzer->eventsStartedByCall as $aEvent}
            <tr style="{$aEvent->cssRowColor} height: 160px;" class="row-{$aEvent->cssIcon}">
                <td>
                    <a name="{$aEvent->event->code}"></a>
                    {$analyzer->getFormattedAEventHeader($aEvent)}
                    <br/>

                    {$i = 1}
                    <table class="table-condensed pull-left" style="margin-top: 4px;">
                        <tbody>
                        <tr>
                            {foreach $aEvent->replicas as $step}
                                <td class="span1" style="width: 140px;">
                                    <!-- step No -->
                                    <span class="badge">{$i}</span> <br/>
                                    {foreach $step as $replica}
                                        <!-- replica No -->
                                        <span class="badge badge-inverse" title="{$replica->text}">
                                            {$replica->replica_number}</span>

                                        <a href="#{$replica->next_event_code}"
                                           title="{$analyzer->getEventTitleByCode($replica->next_event_code)}">
                                            {$replica->next_event_code}</a>
                                            {$analyzer->getFormattedReplicaFlag($replica)}
                                        <br/>
                                        <!-- flag block replica -->
                                        {if (isset($analyzer->flagsBlockReplica[$replica->id])) }
                                            {$replica->replica_number} Need:
                                            {foreach $analyzer->flagsBlockReplica[$replica->id] as $flagBlock}
                                                {$flagBlock->flag_code}<br/>
                                            {/foreach}
                                        {/if}
                                    {/foreach}
                                </td>
                                {$i = $i + 1}
                            {/foreach}
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        {/foreach}
        </table>

        <br/>
        <br/>

        {if ($isDbMode)}
            <a name="behaviours-list"></a>
            <h3>Список поведений пользователя и возможных их проявлений</h3>

            <table class="table">
                <thead>
                    <tr>
                        <th>Код</th>
                        <th>Название</th>
                        <th>Макс. балл</th>
                        <th>Тип шкалы</th>
                        <th>Проявления</th>
                    </tr>
                </thead>
                <tbody>
                {foreach $analyzer->heroBehaviours as $heroBehaviour}
                <tr class="behaviour-{if ($analyzer->isNeverShown($heroBehaviour))}{'never'}{else}{'has'}{/if}-shown">
                    <td>
                        {$heroBehaviour->code}
                    </td>
                    <td>
                        {$heroBehaviour->title}
                    </td>
                    <td>
                        {$heroBehaviour->scale}
                    </td>
                    <td>
                        {Yii::t('site', $heroBehaviour->getTypeScaleSlug())}
                    </td>
                    <td width="50%">
                        {if (0 < count($analyzer->getMailPointsForBehaviour($heroBehaviour)))}
                            Mails: <br/>
                        {/if}
                        {foreach $analyzer->getMailPointsForBehaviour($heroBehaviour) as $mailPoint}
                            <span class="label label-{if (1 == $mailPoint->add_value)}{'success'}{else}{'important'}{/if}"
                                  style="display: inline-block; min-width: 175px;">

                                {if (1 == $mailPoint->add_value)}+{else}&nbsp;&nbsp;{/if}{$mailPoint->add_value} &nbsp; &nbsp;
                                <a href="#{$mailPoint->mail->code}" style="color: #fff;">
                                    {$mailPoint->mail->code}</a>
                                </span>
                            &nbsp;
                        {/foreach}

                        <!-- to separate replicas -->
                        {if (0 < count($analyzer->getMailPointsForBehaviour($heroBehaviour)))}
                            <br/>
                        {/if}

                        {if (0 < count($analyzer->getReplicaPointsForBehaviour($heroBehaviour)))}
                            Replicas:<br/>
                        {/if}
                        {foreach $analyzer->getReplicaPointsForBehaviour($heroBehaviour) as $replicaPoint}
                            <span class="label label-{if (1 == $replicaPoint->add_value)}{'success'}{else}{'important'}{/if}""
                                style="display: inline-block; min-width: 175px;">

                                {if (1 == $replicaPoint->add_value)}+{else}&nbsp;&nbsp;{/if}{$replicaPoint->add_value} &nbsp; &nbsp;
                                <a href="#{$replicaPoint->replica->code}" style="color: #fff; display: inline-block; width: 40px;">
                                    {$replicaPoint->replica->code}
                                </a>
                                step: {$replicaPoint->replica->step_number},
                                <span title="{$replicaPoint->replica->text}">replica: {$replicaPoint->replica->replica_number}</span>
                            </span>
                            &nbsp;
                        {/foreach}
                    </td>
                </tr>
                {/foreach}
                </tbody>
            </table>
        {/if}
    </div>
</div>

<br>
<br>
<br>

<script type="text/javascript">
    // hide dialog`s variants
    $(".switcher").click(function(event){
        event.preventDefault();
        $('.'+$(this).attr('data-id')).toggle();
    });

    $('.variations').toggle();

    // hide/show dialogs
    $('.toggle-dialogs').click(function(){
        $('.row-icon-bell').toggle();
        $('.row-icon-briefcase').toggle();
        $('.row-icon-user').toggle();
        $('.row-icon-comment').toggle();

        if ($(this).hasClass('btn-inverse')) {
            $(this).removeClass('btn-inverse');
            $(this).html('<i class="icon icon-user pull-left" style="margin: 10px 10px 10px 0;"></i>'
                    +'<div class="pull-left">'
                    +'Диалоги видны<br/>'
                    +'(скрыть)'
                    +'</div>');
            $(this).find('i').removeClass('icon-white');
        } else {
            $(this).addClass('btn-inverse');
            $(this).html('<i class="icon icon-white icon-user pull-left" style="margin: 10px 10px 10px 0;"></i>'
                    +'<div class="pull-left">'
                    +'Диалоги скрыты<br/>'
                    +'(показать)'
                    +'</div>');
            $(this).find('i').addClass('icon-white');


        }
    });

    // hide/show emails
    $('.toggle-emails').click(function(){
        $('.row-icon-envelope').toggle();

        if ($(this).hasClass('btn-inverse')) {
            $(this).removeClass('btn-inverse');
            $(this).html('<i class="icon icon-envelope pull-left" style="margin: 10px 10px 10px 0;"></i>'
                    +'<div class="pull-left">'
                    +'Письма видны<br/>'
                    +'(скрыть)'
                    +'</div>');
            $(this).find('i').removeClass('icon-white');
        } else {
            $(this).addClass('btn-inverse');
            $(this).html('<i class="icon icon-white icon-envelope pull-left" style="margin: 10px 10px 10px 0;"></i>'
                    +'<div class="pull-left">'
                    +'Письма скрыты<br/>'
                    +'(показать)'
                    +'</div>');
            $(this).find('i').addClass('icon-white');
        }
    });

    // hide/show behaviour without manifest
    $('.toggle-behaviour-without-manifests').click(function(){
        $('.behaviour-never-shown').toggle();

        if ($(this).hasClass('btn-inverse')) {
            $(this).removeClass('btn-inverse');
            $(this).html('<div class="pull-left">'
                +'Поведения<br/>'
                +'без проявлений<br/>'
                +'показаны'
                +'(скрыть)'
                +'</div>');
            $(this).find('i').removeClass('icon-white');
        } else {
            $(this).addClass('btn-inverse');
            $(this).html('<div class="pull-left">'
                +'Поведения<br/>'
                +'без проявлений<br/>'
                +'показаны'
                +'(показать)'
                +'</div>');
            $(this).find('i').addClass('icon-white');


        }
    });

    // hide/show behaviour with manifest
    $('.toggle-behaviour-with-manifests').click(function(){
        $('.behaviour-has-shown').toggle();

        if ($(this).hasClass('btn-inverse')) {
            $(this).removeClass('btn-inverse');
            $(this).html('<div class="pull-left">'
                    +'Поведения<br/>'
                    +'c проявлениями<br/>'
                    +'показаны'
                    +'(скрыть)'
                    +'</div>');
            $(this).find('i').removeClass('icon-white');
        } else {
            $(this).addClass('btn-inverse');
            $(this).html('<div class="pull-left">'
                    +'Поведения<br/>'
                    +'с проявлениями<br/>'
                    +'показаны'
                    +'(показать)'
                    +'</div>');
            $(this).find('i').addClass('icon-white');


        }
    });

    $('.row-icon-envelope').toggle();

    // flow menu
    $(document).ready(function(){
        $('#flow-menu').portamento();
    });

</script>