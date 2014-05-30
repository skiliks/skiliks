
<h1>Действия с конфигом <?= $config->alias ?></h1>

<table class="table table-bordered" style="width: 600px;">
    <tr>
        <td>Псевдоним</td>
        <td><?= $config->alias ?></td>
    </tr>
    <tr>
        <td>Тип</td>
        <td><?= $config->type ?></td>
    </tr>
    <tr>
        <td>Значение</td>
        <td><?= $config->value ?></td>
    </tr>
    <tr>
        <td>Описание</td>
        <td><?= $config->description ?></td>
    </tr>
</table>

<br/>
<br/>

<a href="/admin_area/project_configs/list">
    &lt;- Назад, к списку конфигов
</a>

<br/>
<br/>
<br/>

<table class="table">
    <thead>
    <tr>
        <th>Инициатор</th>
        <th>Время</th>
        <th>Запись</th>
    </tr>
    </thead>
    <tbody>

    </tbody>
    <?php /** @var SiteLogProjectConfig $log */ ?>
    <?php foreach ($logs as $log) : ?>
        <tr>
            <td><?= $log->user->profile->email ?></td>
            <td><?= $log->created_at ?></td>
            <td><?= $log->log ?></td>
        </tr>
    <?php endforeach ?>
</table>
