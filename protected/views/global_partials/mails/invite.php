<?php /** @var Invite $invite */ ?>

Приветствуем, <?= $invite->getReceiverUserName() ?>!
<br/>
<?= $invite->ownerUser->account_corporate->company_name ?: 'Компания' ?> предлагает вам пройти тест «Базовый менеджмент» для участия в конкурсе на вакансию <a href="<?= $invite->vacancy->link ?: '#' ?>"><?= $invite->vacancy->label ?></a>.
<br/>
<?php if (empty($invite->receiverUser)): ?>
<a href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">«Базовый менеджмент»</a> - это деловая симуляция, позволяющая оценить менеджерские навыки в форме увлекательной игры.
<br/>
<?php endif; ?>

<?= $invite->message ?>
<br/>
<?php if ($invite->receiverUser): ?>
Пожалуйста, <a href="<?= $this->createAbsoluteUrl('static/pages/product') ?>">зайдите</a> в свой кабинет и примите приглашение на тестирование для прохождения симуляции.
<?php else: ?>
Пожалуйста, <a href="<?= $invite->getInviteLink() ?>">зарегистрируйтесь</a> и в своем кабинете примите приглашение на тестирование для прохождения симуляции.
<?php endif; ?>
<br/>
Ваш skiliks