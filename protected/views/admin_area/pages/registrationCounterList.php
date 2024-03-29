<h2>По дням</h2>

<table class="table table-striped">
    <tr>
        <th>День</th>
        <?php foreach($registrationsByDay as $day) : ?>
            <th><?=$day["period"] ?></th>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего регистраций</th>
        <?php foreach($registrationsByDay as $day) : ?>
            <td><?=$day["totalRegistrations"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего персональных</th>
        <?php foreach($registrationsByDay as $day) : ?>
            <td><?=$day["totalPersonals"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего не активных персональных</th>
        <?php foreach($registrationsByDay as $day) : ?>
            <td><?=$day["totalNonActivePersonals"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего корпоративных</th>
        <?php foreach($registrationsByDay as $day) : ?>
            <td><?=$day["totalCorporate"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего не активных корпоративных</th>
        <?php foreach($registrationsByDay as $day) : ?>
            <td><?=$day["totalNonActiveCorporate"] ?></td>
        <? endforeach; ?>
    </tr>
</table>

<h2>По месяцам</h2>

<table class="table table-striped">
    <tr>
        <th>Месяц</th>
        <?php foreach($registrationsByMonth as $day) : ?>
            <th><?=$day["period"] ?></th>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего регистраций</th>
        <?php foreach($registrationsByMonth as $day) : ?>
            <td><?=$day["totalRegistrations"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего персональных</th>
        <?php foreach($registrationsByMonth as $day) : ?>
            <td><?=$day["totalPersonals"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего не активных персональных</th>
        <?php foreach($registrationsByMonth as $day) : ?>
            <td><?=$day["totalNonActivePersonals"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего корпоративных</th>
        <?php foreach($registrationsByMonth as $day) : ?>
            <td><?=$day["totalCorporate"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего не активных корпоративных</th>
        <?php foreach($registrationsByMonth as $day) : ?>
            <td><?=$day["totalNonActiveCorporate"] ?></td>
        <? endforeach; ?>
    </tr>
</table>

<h2>За год</h2>

<table class="table table-striped">
    <tr>
        <th>Год</th>
        <?php foreach($registrationsByYear as $year => $day) : ?>
            <th><?=$registrationsByYearOld[$year-1]["period"] ?></th>
            <th><?=$day["period"] ?></th>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего регистраций</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$registrationsByYearOld[$year-1]["totalRegistrations"] ?></td>
            <td><?=$day["totalRegistrations"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего персональных</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$registrationsByYearOld[$year-1]["totalPersonals"] ?></td>
            <td><?=$day["totalPersonals"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего не активных персональных</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$registrationsByYearOld[$year-1]["totalNonActivePersonals"] ?></td>
            <td><?=$day["totalNonActivePersonals"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего корпоративных</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$registrationsByYearOld[$year-1]["totalCorporate"] ?></td>
            <td><?=$day["totalCorporate"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего не активных корпоративных</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$registrationsByYearOld[$year-1]["totalNonActiveCorporate"] ?></td>
            <td><?=$day["totalNonActiveCorporate"] ?></td>
        <? endforeach; ?>
    </tr>
</table>