<?php $titles = [
    'Тема',
    'Сообщение',
    'Email отправителя',
    'Дата создания',
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
        <?php foreach($feedbacks as $feedback) : ?>
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
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>