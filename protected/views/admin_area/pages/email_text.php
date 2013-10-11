<table class="table table-bordered">
    <tr>
        <td>От кого:</td>
        <td><?= $email->sender_email ?></td>
    </tr>
    <tr>
        <td>Кому:</td>
        <td><?= $email->recipients ?></td>
    </tr>
    <tr>
        <td>Тема:</td>
        <td><?= $email->subject ?></td>
    </tr>
    <tr>
        <td>Дата написания / отправки</td>
        <td>
            <?= $email->created_at ?><br/>
            <?= (null == $email->sended_at) ? 'не отправлено' : $email->sended_at ?>
        </td>
    </tr>
</table>

<br/>

<?= $email->body ?>