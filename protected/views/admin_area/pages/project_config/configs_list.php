
<h1>Настройки проекта</h1>

<table class="table">
    <thead>
        <tr>
            <th>Псевдоним</th>
            <th>Значение</th>
            <th>Тип</th>
            <th>Используется ли в симуляции?</th>
            <th>Комментарий</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php /** @var ProjectConfig $config */ ?>
        <?php foreach ($configs as $config): ?>
            <tr>
                <td><?= $config->alias ?></td>
                <td><?= $config->value ?></td>
                <td><?= $config->type ?></td>
                <td><?= ProjectConfig::$is_use_in_sim[$config->is_use_in_simulation] ?></td>
                <td><?= $config->description ?></td>
                <td>
                    <a class="btn btn-success" style="white-space:nowrap;"
                       href="/admin_area/project_configs/add?id=<?= $config->id ?>">
                        <i class="icon icon-pencil icon-white"></i> &nbsp; редактировать
                    </a>
                </td>
                <td>
                    <a class="btn btn-info" style="white-space:nowrap;"
                       href="/admin_area/project_configs/log/<?= $config->id ?>">
                        логи
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<a href="/admin_area/project_configs/add" class="btn btn-success">Добавить</a>