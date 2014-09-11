
<?php if (Yii::app()->user->data()->can('sim_site_logs_view')): ?>
    <a class="btn btn-info" href="/admin_area/simulation/<?= $simulation->id?>/site-logs">
        Смотреть логи сайта
    </a>
    &nbsp;&nbsp;
<?php endif ?>

<?php if (Yii::app()->user->data()->can('sim_server_requests_list_view')): ?>
<a class="btn btn-info" href="/admin_area/simulation/<?= $simulation->id?>/requests">
    Смотреть запросы
</a>
&nbsp;&nbsp;
<?php endif ?>