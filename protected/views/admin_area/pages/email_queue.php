<?php $titles = [
    'Дата написания,<br/>Дата отправки',
    'Статус',
    'От кого',
    'Кому',
    'Тема',
    'Полный текст',
] ?>
<div class="row fix-top">
    <h2>Очередь писем</h2>

    <div class="row">
        <form class="form-inline" action="/admin_area/email_queue" method="post">
            <table class="table table-bordered" style="margin-left: 40px; width: 80%;">
                <tr>
                    <td>Е-мейл отправителя</td>
                    <td colspan="3">
                        <input type="text" style="margin-left:40px;" value="<?=$filters['sender_email'] ?>"
                            name="sender_email" placeholder="email">
                    </td>
                </tr>

                <tr>
                    <td>Е-мейл получателя</td>
                    <td colspan="3">
                        <input type="text" style="margin-left:40px;" value="<?=$filters['recipients'] ?>"
                            name="recipients" placeholder="email">
                    </td>
                </tr>

                <tr>
                    <td>Статус</td>
                    <td>
                        <button class="btn btn-check-all">Отметить все</button>
                        <button class="btn btn-uncheck-all">Снять все</button>
                    </td>
                    <td><input type="checkbox" name="send" id="send" value="done"
                            <?= ($filters["send"] !== null) ? 'checked' : '' ?>>
                         <label for="done">Отправлено</label></td>
                    <td><input type="checkbox" name="not_send" id="not_send" value="not_send"
                            <?= ($filters["not_send"] !== null) ? 'checked' : '' ?>>
                         <label for="not_send">Не отправлено</label>
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        <a style="margin-left:20px;" name="form-send" class="btn" href="/admin_area/email_queue">Снять фильтры</a>
                        <input style="margin-left:20px;" type="submit"
                            name="form-send" class="btn btn-success" value="Применить фильтры">
                    </td>
                </tr>
            </table>
        </form>
    </div>

    <?php $this->widget('CLinkPager',array(
        'header'         => '',
        'pages'          => $pager,
        'maxButtonCount' => 5, // максимальное вол-ко кнопок
    )); ?>

    Страница <?= $page ?> из <?= ceil($totalItems/$itemsOnPage) ?> (<?= $itemsOnPage ?> записей на странице из <?= $totalItems ?>)

    <br/>
    <br/>
    <table class="table table-hover">
        <thead>
        <tr>
            <?php foreach($titles as $title) :?>
            <th><?=$title?></th>
            <?php endforeach ?>
        </tr>
        </thead>
        <tbody>
        <?php /* @var $email Invoice */ ?>
        <?php $step = 9; $i = 0; ?>
        <?php foreach($emails as $email) : ?>
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
            <td style="width: 150px;">
                <?= $email->created_at ?><br/>
                <?= (null == $email->sended_at) ? 'не отправлено' : $email->sended_at ?>
            </td>

            <td>
                <?= (null == $email->sended_at)
                    ? '<span class="label label-important">не отправлено</span>'
                    : '<span class="label label-success">отправлено</span>' ?>
            </td>

            <td>
                <?= $email->sender_email ?>
            </td>

            <td>
                <?= $email->recipients ?>
            </td>
            <td>
                <?= $email->subject ?>
            </td>
            <td>
                <a href="/admin_area/email/<?= $email->id ?>/text" target="_blank">Письмо</a>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
</div>
