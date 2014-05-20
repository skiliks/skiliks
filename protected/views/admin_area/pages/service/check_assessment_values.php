<h1>Список проверок итоговых оценок (по всем симуляциям)</h1>

<br/>

<?php if (null == $currentCheck) : ?>
    <a href="?action=check" class="btn btn-success action-check-assessment-results">
        <i class="icon icon-check icon-white"></i> Выполнить проверку
    </a>
<?php else: ?>
    В данный момент уже идёт проверка
<?php endif; ?>
(ориентировочная длительность 1 мин)

<br/>
<br/>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Начата в</th>
            <th>Статус</th>
            <th>Завершена в</th>
            <th>Инициатор</th>
            <th><!-- Показать/скрыть лог --></th>
            <th>Результирующий лог</th>
        </tr>
    </thead>

    <tbody>
        <?php $i = 0; ?>
        <?php foreach ($allCheckLogs as $checkLog) : ?>
            <?php /** @var SiteLogCheckResults $checkLog */ ?>
            <tr>
                <td>
                    <?= $checkLog->id ?>
                </td>
                <td>
                    <?= $checkLog->started_at ?>
                </td>
                <td>
                    <?php if (null == $checkLog->finished_at) : ?>
                        <span class="label">В прогрессе</span>
                    <?php else: ?>
                        <?php if (strlen($checkLog->result) < 1041) : ?>
                            <span class="label label-success"> <i class="icon icon-ok icon-white"></i> OK</span>
                        <?php else: ?>
                            <span class="label label-important"> <i class="icon icon-warning-sign icon-white"></i> Есть ошибки</span>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <td>
                    <?= $checkLog->finished_at ?>
                </td>
                <td>
                    <?= $checkLog->startedBy->profile->email ?>
                </td>
                <td>
                    <span class="btn action-swich-<?= $i ?>">Показать/скрыть лог</span>
                </td>
                <td>
                    <?php // для активной симуляции лог должен быть показан, прогресс
                          //- это именно то, что хочет сейчас видеть админ ?>
                    <span class="locator-data-<?= $i ?>"
                          <?= (null == $checkLog->finished_at) ? '' : 'style="display: none;"'; ?> >
                        <?= $checkLog->result ?>
                    </span>
                    <script type="text/javascript">
                        $('.action-swich-<?= $i ?>').click(function() {
                            $('.locator-data-<?= $i ?>').toggle();
                        });
                    </script>
                </td>
            </tr>
            <?php $i++; ?>
        <?php endforeach; ?>
    </tbody>
</table>

<?php // обновляем страницу автоматически, но только если есть "активная" проверка ?>
<?php if (null != $currentCheck) : ?>
    <script type="text/javascript">
        setTimeout('location.reload(false)', 15*1000); // 15 sec
    </script>
<?php endif; ?>