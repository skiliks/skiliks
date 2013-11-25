<?php $this->renderPartial('//admin_area/partials/_user_info', ['simulation'=>$simulation]) ?>

<br/>

<table class="table table-bordered" border="3px">
    <thead>
        <tr>
            <th>Реальное время (UID) <strong>Игровое время</strong> , URL</th>
            <th>Запрос</th>
            <th>Ответ</th>
        </tr>
    </thead>
    <?php foreach ($simulationLogs as $simulationLog) : ?>
        <tr style="height: 120px;">
            <td>
                <?= $simulationLog->real_time ?> (<?= $simulationLog->request_uid?>)
                <strong><?= $simulationLog->frontend_game_time ?></strong>,

                <br/>
                <?= str_replace('/index.php/', '', $simulationLog->request_url) ?>
            </td>
            <td style="padding: 5px;">
                <div style="width: 500px; height: 120px; overflow: auto;">
                    <?= str_replace(',', ', ', $simulationLog->request_body) ?>
                </div>
            </td>
            <td style="padding: 5px;">
                <div style="width: 500px; height: 120px; overflow: auto;">
                    <?= str_replace(',', ', ', $simulationLog->response_body) ?>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
</table>