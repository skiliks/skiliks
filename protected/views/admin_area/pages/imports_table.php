
<br/>
<br/>

<h1>Реимпорты</h1>

<br/>

<h4>Текущее версии</h4>

<table class="table table-bordered">
    <?php foreach ($scenarios as $scenario): ?>
        <tr>
            <td><?php echo $scenario->slug ?></td>
            <td><?php echo $scenario->filename ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h4>Сделать реимпорт</h4>

<span data-scenario="<?= Scenario::TYPE_FULL ?>" class="btn btn-success start-import">Full сценарий</span>
&nbsp;
<span data-scenario="<?= Scenario::TYPE_LITE ?>" class="btn btn-success start-import">Lite сценарий</span>
&nbsp;
<span data-scenario="<?= Scenario::TYPE_TUTORIAL ?>" class="btn btn-success start-import">Tutorial сценарий</span>

<div id="import-console" style="display: none;">
<br/>
    <strong>Import log: </strong> <span id="import-scenario-slug"></span>
    <a id="refresh" style="display: none;" href="<?= Yii::app()->request->url ?>">
        <i class="icon-refresh"></i>
        Обновить страницу
    </a>
    <div id="import-console-text" style="width: 100%; height: 280px; border: 1px solid #000; overflow-y: scroll;"></div>
</div>

<script type="text/javascript">
    $('.start-import').click(function() {

        // protection for double click {
        if (false === $(this).hasClass('btn-success')) {
            return;
        }
        $('.start-import').removeClass('btn-success');
        // protection for double click }

        $('#import-console').show();
        $('#import-console-text')
            .html('Импорт начался. </br> Идёт поиск и парсинг сценария.');

        var slug = $(this).attr('data-scenario');
        $('#import-scenario-slug').text(slug + '...');
        window.logId = 0;

        $.ajax({
            url: '/admin_area/import-log/0/get-text',
            type: 'post',
            async: false,
            complete: function (data) {
                json = $.parseJSON(data.responseText);
                window.logId = json.log_id;
            }
        });

        // init import:
        $.ajax({
            url: '/admin_area/import-scenario/' + slug + '/' + window.logId,
            type: 'post'
        });

        // init update ".import-console" data
        window.callbackGetImportLogData = function() {
            $.ajax({
                url: '/admin_area/import-log/' + logId + '/get-text',
                type: 'post',
                complete: function (data) {
                    json = $.parseJSON(data.responseText);
                    if (json.text) {
                        $('#import-console').show();
                        $('#import-console-text')
                            .html(json.text.replace(/\r\n/g, "<br />").replace(/\n/g, "<br />"));
                        $('#import-console-text').scrollTo('100%', 0);
                    }
                    if(undefined !== typeof json.finish_time && json.finish_time != null) {
                        // import finished
                        window.clearInterval(window.getStatus);
                        $('.start-import').addClass('btn-success');
                        $('#refresh').show();
                    }
                }
            });
        }

        window.getStatus = setInterval("window.callbackGetImportLogData();", 1000);
    });
</script>

<?php if (0 < count($logs)) : ?>
    <br/>
    <br/>
    <h4>Последние 10 импортов</h4>
    <br/>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <th>Scenario</th>
            <th>User</th>
            <th>Started at</th>
            <th>Finished at</th>
            <th>Log</th>
        </tr>
        <?php foreach ($logs as $log) : ?>
            <tr>
                <td><?= $log->id ?></td>
                <td><?= (null !== $log->scenario ) ? $log->scenario->filename : '--' ?></td>
                <td>
                    <?= (null !== $log->user ) ? $log->user->profile->firstname : '-' ?>
                    <?= (null !== $log->user ) ? $log->user->profile->lastname : '-' ?>
                </td>
                <td><?= $log->started_at ?></td>
                <td><?= $log->finished_at ?></td>
                <td>
                    <div class="hide-show-log-<?= $log->id ?> btn btn-info">Hide/show</div>
                    <div class="log-<?= $log->id ?>" style="display: none;">
                        <?php if (null == $log->text): ?>
                            <?php if (is_file(__DIR__.'/../../../logs/'.$log->id.'-import.log')): ?>
                                <?= nl2br(file_get_contents(__DIR__.'/../../../logs/'.$log->id.'-import.log')) ?>
                            <?php endif ?>
                        <?php else : ?>
                            <?= nl2br($log->text) ?>
                        <?php endif ?>
                    </div>
                    <script type="text/javascript">
                        $('.hide-show-log-<?= $log->id ?>').click(function() {
                            $('.log-<?= $log->id ?>').toggle();
                        });
                    </script>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
<?php endif ?>