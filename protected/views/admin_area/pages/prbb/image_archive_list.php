<h1>Список архивов сгенерированных для ПРББ</h1>

<br/>

<?php if (null == $currentTask) : ?>
    <form action="" method="post">
        <input type="hidden" name="action" value="generate" />

        Введите sim ids: &nbsp; <input type="text" name="simIds" />

        <input type="submit" value="Сгенерировать картинки"
            style="margin: -10px 0 0 10px;"
            class="btn btn-success action-check-assessment-results" />
    </form>
    (ориентировочная длительность 2 мин на одну симуляцию)
<?php else: ?>
    В данный момент уже идёт генерация картинок.
    (ориентировочная длительность <?= count($simulationIds) * 2 ?> мин)
<?php endif; ?>


<br/>
<br/>

<table class="table">
    <thead>
    <tr>
        <th>ID</th>
        <th>Начата в</th>
        <th>Завершена в</th>
        <th>Статус</th>
        <th>Инициатор</th>
        <th>Скачать</th>
        <th>Результирующий лог</th>
        <th></th>
    </tr>
    </thead>

    <tbody>
    <?php $i = 0; ?>
    <?php foreach ($allTasks as $task) : ?>
        <?php /** @var SiteLogGeneratePrbbFiles $task */ ?>
        <tr>
            <td>
                <?= $task->id ?>
            </td>
            <td>
                <?= $task->started_at ?>
            </td>
            <td>
                <?= $task->finished_at ?>
            </td>
            <td>
                <?php if (null == $task->finished_at) : ?>
                    <span class="label">В прогрессе</span>
                <?php else: ?>
                    <span class="label label-success"> <i class="icon icon-ok icon-white"></i> Завершена</span>
                <?php endif; ?>
            </td>
            <td>
                <?= $task->startedBy->profile->email ?>
            </td>
            <td>
                <?php if (null !== $task->path) : ?>
                    <a class="btn btn-success"
                        href="/admin_area/prbb/images-zip/<?= $task->id?>/download">
                        <i class="icon icon-download icon-white"></i> &nbsp;
                        Скачать
                    </a>
                <?php endif; ?>
            </td>
            <td>
                <span class="btn action-swich-<?= $i ?>">Показать/скрыть лог</span>
            </td>
            <td>
                <?php // для активной симуляции лог должен быть показан, прогресс
                //- это именно то, что хочет сейчас видеть админ ?>
                <span class="locator-data-<?= $i ?>"
                    <?= (null == $task->finished_at) ? '' : 'style="display: none;"'; ?> >
                        <?= $task->result ?>
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
<?php if (null != $currentTask) : ?>
    <script type="text/javascript">
        setTimeout('location.reload(false)', 15*1000); // 15 sec
    </script>
<?php endif; ?>