<h1>Список сгенерированных сводных аналитических файлов</h1>

<br/>

<?php if (null == $generatedFile) : ?>
    <a href="?action=generate" class="btn btn-success action-check-assessment-results">
        <i class="icon icon-check icon-white"></i> Сгенерировать файл
    </a>
<?php else: ?>
    В данный момент уже идёт проверка
<?php endif; ?>
&nbsp;
&nbsp;
(ориентировочная длительность 30 мин)
&nbsp;
&nbsp;
<a class="btn btn-success" href="/admin_area/downloadFullAnalyticFile">
    <i class="icon icon-download icon-white"></i>
    Скачать последний сгенерированный файл
</a>

<br/>
<br/>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Начата в</th>
        <th>Завершена в</th>
        <th>Инициатор</th>
        <th>Результирующий лог</th>
    </tr>
    </thead>

    <tbody>
    <?php $i = 0; ?>
    <?php foreach ($allFiles as $log) : ?>
        <?php /** @var SiteLogGenerateConsolidatedAnalyticFile $log */ ?>
        <tr>
            <td>
                <?= $log->id ?>
            </td>
            <td>
                <?= $log->started_at ?>
            </td>
            <td>
                <?= $log->finished_at ?>
            </td>
            <td>
                <?= $log->startedBy->profile->email ?>
            </td>
            <td>
                <?= $log->result ?>
            </td>
        </tr>
        <?php $i++; ?>
    <?php endforeach; ?>
    </tbody>
</table>

<?php // обновляем страницу автоматически, но только если есть "активная" проверка ?>
<?php if (null != $generatedFile) : ?>
    <script type="text/javascript">
        setTimeout('location.reload(false)', 15*1000); // 15 sec
    </script>
<?php endif; ?>