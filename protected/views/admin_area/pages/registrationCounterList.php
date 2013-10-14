<h2>По дням</h2>

<table class="table table-striped">
    <tr>
        <th>День</th>
        <?php foreach($registrationsByDay as $day) : ?>
            <td><?=$day["period"] ?></td>
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
            <td><?=$day["period"] ?></td>
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
        <th>День</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$day["period"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего регистраций</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$day["totalRegistrations"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего персональных</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$day["totalPersonals"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего не активных персональных</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$day["totalNonActivePersonals"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего корпоративных</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$day["totalCorporate"] ?></td>
        <? endforeach; ?>
    </tr>

    <tr>
        <th>Всего не активных корпоративных</th>
        <?php foreach($registrationsByYear as $day) : ?>
            <td><?=$day["totalNonActiveCorporate"] ?></td>
        <? endforeach; ?>
    </tr>
</table>