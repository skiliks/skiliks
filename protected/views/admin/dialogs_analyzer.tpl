<style>
    .badge-inverse:hover {
        background-color: #FFD324;
    }
</style>

<h1> Анализатор диалогов </h1>
Источник: {$sourceName}.

<table class="table">
    <thead>
        <tr>
            <td>События начинающиеся по времени</td>
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
                <tr class="" style="{$aEvent->cssRowColor}">
                    <td class="span4">
                        <a name="{$aEvent->event->code}"></a>
                        <i class="icon-time"></i> {$aEvent->startTime}
                        / <strong>{$aEvent->event->code}</strong> <br/>
                        <i class="{$aEvent->cssIcon}"></i> {$aEvent->title} <br/>

                        <!-- Задержка -->
                        Задержка: {$aEvent->delay} мин<br/>
                        Длительность: c {$aEvent->startTime} до {$aEvent->durationFrom} ~ {$aEvent->durationTo}<br/>

                        {if (0 !== count($aEvent->flagsToSwitch))}
                        Может включить флаги: <strong>{implode(', ', array_keys($aEvent->flagsToSwitch))}</strong>
                        {/if}
                        <br/>

                        <!-- Является последствием -->
                        {if (0 != count($aEvent->producedBy))}
                            <i class="icon-arrow-right" title="{$aEvent->event->code} является последствием ... "></i> :
                        {/if}

                        {foreach $aEvent->producedBy as $key => $value}
                            <a href="#{$key}" title="{$analyzer->getEventTitleByCode($key)}">
                                <span class="label label-warning">{$key}</span></a>
                        {/foreach}

                        {$i = 1}
                        <table class="table-condensed pull-right" style="margin-top: -101px;">
                            <tbody>
                                <tr>
                                {foreach $aEvent->replicas as $step}
                                    <td class="span1" style="width: 80px;">
                                    <span class="badge">{$i}</span> <br/>
                                    {foreach $step as $replica}
                                        <span class="badge badge-inverse" title="{$replica->text}">
                                            {$replica->replica_number}</span>

                                            <a href="#{$replica->next_event_code}"
                                               title="{$analyzer->getEventTitleByCode($replica->next_event_code)}">
                                                {$replica->next_event_code}</a>
                                        <br/>
                                    {/foreach}
                                    </td>
                                    {$i = $i + 1}
                                {/foreach}
                                </tr>
                            </tbody>
                        </table>

                        {if (isset($analyzer->tree[$aEvent->event->code]))}
                            <a data-id="{$aEvent->event->code}-variations" class="switcher">Скрыть/показать варианты развития события</a>
                        {/if}
                    </td>
                </tr>
                {if (isset($analyzer->tree[$aEvent->event->code]))}
                    {foreach $analyzer->tree[$aEvent->event->code] as $branch}
                        <tr class="{$aEvent->event->code}-variations variations">
                            <td>
                            {$i = 0}
                            {foreach $branch as $element}
                                <div class="pull-left" style="width: 100px;">
                                    <a href="#{$element['prevCode']}"
                                       title="{$analyzer->getReplicaHintByCodeStepReplicaNumber($element['prevCode'],$element['step'],$element['replica'])}">
                                        {$element['prevCode']}[{$element['step']}-{$element['replica']}]</a> <br/>
                                    {$element['startTime']} <br/>
                                </div>

                                <!-- Показать результируюшее событие -->
                                {$i = $i + 1}
                                {if ($i == count($branch))}
                                    <div class="pull-left" style="width: 100px;">
                                        {$element['code']}
                                        {if (null !== $element['flag'])}
                                            + {$element['flag']}=>1
                                        {/if}
                                        <br/>
                                        {$element['startTime']}<br/>
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

<table class="table">
    <thead>
    <tr>
        <td>События начинающиеся по вызову из диалога</td>
    </tr>
    </thead>
{foreach $analyzer->eventsStartedByCall as $aEvent}
    <tr style="{$aEvent->cssRowColor}">
        <td>
            <a name="{$aEvent->event->code}"></a>
            <strong>{$aEvent->event->code}</strong> <br/>
            <i class="{$aEvent->cssIcon}"></i> {$aEvent->title} <br/>

            <!-- Задержка -->
            Задержка: {$aEvent->delay} мин<br/>

            <!-- Является последствием -->
            {if (0 != count($aEvent->producedBy))}
                <i class="icon-arrow-right" title="{$aEvent->event->code} является последствием ... "></i> :
                {foreach $aEvent->producedBy as $key => $value}
                    <a href="#{$key}" title="{$analyzer->getEventTitleByCode($key)}">
                        <span class="label label-warning">{$key}</span></a>
                {/foreach}
            {else}
                <span class="label label-important">Никогда не будет вызван!</span>
            {/if}
            <br/>

            {$i = 1}
            <table class="table-condensed pull-right" style="margin-top: -81px;">
                <tbody>
                <tr>
                    {foreach $aEvent->replicas as $step}
                        <td class="span1" style="width: 80px;">
                            <span class="badge">{$i}</span> <br/>
                            {foreach $step as $replica}
                                <span class="badge badge-inverse" title="{$replica->text}">
                                    {$replica->replica_number}</span>

                                <a href="#{$replica->next_event_code}"
                                   title="{$analyzer->getEventTitleByCode($replica->next_event_code)}">
                                    {$replica->next_event_code}</a>
                                <br/>
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
</script>