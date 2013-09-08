
<h1>Список подписавшихся:</h1>

<br/>

<?php foreach ($subscribersEmails as $email) : ?>
    <?= $email['id'] ?>. <?= $email['email'] ?> <br>
<?php endforeach; ?>