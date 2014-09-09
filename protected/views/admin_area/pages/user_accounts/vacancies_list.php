
<h1>Позиции пользователя <?= $user->profile->email ?></h1>

<a href="/admin_area/user/<?= $user->id ?>/details">
    &lt;- Вернуться назад
</a>

<br/>
<br/>

<table class="table">
    <thead>
        <tr>
            <th>Название</th>
            <th>Ссылка</th>
            <?php if (Yii::app()->user->data()->can('user_edit_vacations')) : ?>
                <th colspan="2"></th>
            <?php endif ?>
        </tr>
    </thead>
    <tbody>
        <?php if (0 == count($vacancies)) : ?>
            <tr>
                <td colspan="4">
                    У пользователя нет вакансий.
                </td>
            </tr>
        <?php endif ?>
        <?php foreach ($vacancies as $vacancy) : ?>
            <?php /** @var Vacancy $vacancy */ ?>
            <tr>
                <td>
                    <span class="locator-label">
                        <?= $vacancy->label ?>
                    </span>
                </td>
                <td>
                    <span class="locator-link">
                        <?= (null == $vacancy->link) ? '--' : $vacancy->link ?>
                    </span>
                </td>

                <?php if (Yii::app()->user->data()->can('user_edit_vacations')) : ?>
                    <td>
                        <a href="/admin_area/user/<?= $user->id ?>/vacancy/<?= $vacancy->id ?>/remove" class="btn btn-danger">
                            <i class="icon icon-remove icon-white"></i> Удалить
                        </a>
                    </td>
                    <td>
                        <a href="/admin_area/user/<?= $user->id ?>/vacancy/add?id=<?= $vacancy->id ?>" class="btn btn-success">
                            <i class="icon icon-pencil icon-white"></i> Редактировать
                        </a>
                    </td>
                <?php endif ?>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?php if (Yii::app()->user->data()->can('user_edit_vacations')) : ?>
    <a href="/admin_area/user/<?= $user->id ?>/vacancy/add" class="btn btn-success">
        <i class="icon icon-plus icon-white"></i> Добавить
    </a>
<?php endif ?>