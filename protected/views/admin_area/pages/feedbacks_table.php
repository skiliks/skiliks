<?php $titles = [
    'Тема',
    'Сообщение',
    'Email отправителя',
    'Дата создания',
    'IP Address',
    'Коментарий',
    '',
    '',
] ?>
<div class="row fix-top">
    <h2>Отзывы</h2>

    <table class="table table-hover">
        <thead>
        <tr>
            <?php foreach($titles as $title) :?>
                <th><?=$title?></th>
            <?php endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php /* @var $model Invoice */ ?>
        <?php $step = 12; $i = 0; ?>
        <?php foreach($feedbacks as $col => $feedback) : ?>
            <?php $i++ ?>
            <?php if($i === $step) : ?>
                <tr>
                    <?php foreach($titles as $title) :?>
                        <th><?=$title?></th>
                    <?php endforeach ?>
                </tr>
                <?php $i= 0 ?>
            <?php endif ?>
            <tr class="orders-row">
                <td><?= $feedback->theme ?></td>
                <td><?= $feedback->message ?></td>
                <td><?= $feedback->email ?></td>
                <td><?= $feedback->addition ?></td>
                <td><?= ($feedback->ip_address !== null) ? $feedback->ip_address : '-' ?></td>
                <td class="feedback-comment" data-feedback-id="<?=$feedback->id?>">
                    <?= $feedback->comment ?>
                </td>
                <td class="feedback-edit"><a class="btn btn-success feedback-edit-button">Редактировать</a></td>
                <td>
                    <?php if($feedback->is_processed === '0') : ?>
                        <a class="btn btn-success" href="/admin_area/feedbacks?is_action=yes&is_processed=1&id=<?= $feedback->id?>">Отметить как обработанный</a>
                    <?php else : ?>
                        <a class="btn btn-success" href="/admin_area/feedbacks?is_action=yes&is_processed=0&id=<?= $feedback->id?>">Отметить как не обработанный</a>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>