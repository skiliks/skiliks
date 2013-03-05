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

<div id="flow-menu-wrapper" class="row" style="overflow: hidden; width: 1400px;">
    <div class="span2">
        <div id="flow-menu">
            <span class="btn btn-inverse toggle-dialogs" style="width: 120px; text-align: left;">
                <i class="icon icon-white icon-user pull-left" style="margin: 10px 10px 10px 0;"></i>
                <div class="pull-left">
                Убрать<br/>
                диалоги
                </div>
            </span>
            <br/>
            <br/>
            <span class="btn toggle-emails" style="width: 120px; text-align: left;">
                <i class="icon icon-envelope pull-left" style="margin: 10px 10px 10px 0;"></i>
                <div class="pull-left">
                Показать<br/>
                письма
                </div>
            </span>
        </div>
    </div>

    <div class="span10" style="width: 1000px;">
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

                                <!-- Является последствием -->
                                {if (0 != count($aEvent->producedBy))}
                                    <i class="icon-arrow-right" title="{$aEvent->event->code} является последствием ... "></i> :
                                {/if}

                                {foreach $aEvent->producedBy as $key => $value}
                                    <a href="#{$key}" title="{$analyzer->getEventTitleByCode($key)}">
                                        <span class="label label-warning">{$key}</span></a>
                                {/foreach}

                                {$i = 1}
                                <table class="table-condensed pull-left" style="margin-top: 0px;">
                                    <tbody>
                                        <tr>
                                        {foreach $aEvent->replicas as $step}
                                            <td class="span1" style="width: 140px;">
                                            <span class="badge">{$i}</span> <br/>
                                            {foreach $step as $replica}
                                                <span class="badge badge-inverse" title="{$replica->text}">
                                                    {$replica->replica_number}</span>

                                                    <a href="#{$replica->next_event_code}"
                                                       title="{$analyzer->getEventTitleByCode($replica->next_event_code)}">
                                                        {$replica->next_event_code}</a>
                                                        {$analyzer->getFormattedReplicaFlag($replica)}
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
                        {if (isset($analyzer->tree[$aEvent->event->code]))}
                            {foreach $analyzer->tree[$aEvent->event->code] as $branch}
                                <tr class="{$analyzer->getCssSaveEventCode($aEvent)}-variations variations">
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

        <table class="table" style="width: 1400px;">
            <thead>
            <tr>
                <td><h3>События начинающиеся по вызову из <диалога></диалога></h3></td>
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
                                    <span class="badge">{$i}</span> <br/>
                                    {foreach $step as $replica}
                                        <span class="badge badge-inverse" title="{$replica->text}">
                                            {$replica->replica_number}</span>

                                        <a href="#{$replica->next_event_code}"
                                           title="{$analyzer->getEventTitleByCode($replica->next_event_code)}">
                                            {$replica->next_event_code}</a>
                                            {$analyzer->getFormattedReplicaFlag($replica)}
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
                    +'Показать<br/>'
                    +'диалоги'
                    +'</div>');
            $(this).find('i').removeClass('icon-white');
        } else {
            $(this).addClass('btn-inverse');
            $(this).html('<i class="icon icon-white icon-user pull-left" style="margin: 10px 10px 10px 0;"></i>'
                    +'<div class="pull-left">'
                    +'Убрать<br/>'
                    +'диалоги'
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
                    +'Показать<br/>'
                    +'письма'
                    +'</div>');
            $(this).find('i').removeClass('icon-white');
        } else {
            $(this).addClass('btn-inverse');
            $(this).html('<i class="icon icon-white icon-envelope pull-left" style="margin: 10px 10px 10px 0;"></i>'
                    +'<div class="pull-left">'
                    +'Убрать<br/>'
                    +'письма'
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