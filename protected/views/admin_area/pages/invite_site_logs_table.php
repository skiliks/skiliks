<? $titlesInvite = [

];
?>

<div class="row fix-top">

    <!-- Invite: -->

    <h2>Лог операций над приглашением</h2>
    <table class="table table-hover">
        <thead>
        <tr>
            <? foreach($titlesInvite as $title) :?>
                <th><?=$title?></th>
            <? endforeach ?>
        </tr>
        </thead>
        <tbody>
        <? /* @var $model Invite*/ ?>
        <? $step = 12; $i = 0; ?>
        <? foreach($logInvite as $itemI) : ?>
            <? $i++ ?>
            <? if($i === $step) : ?>
                <tr>
                    <? foreach($titlesInvite as $title) :?>
                        <th><?=$title?></th>
                    <? endforeach ?>
                </tr>
                <? $i= 0 ?>
            <? endif ?>
            <tr class="invites-row">
                <td></td>
            </tr>
        <? endforeach ?>
        </tbody>
    </table>

    <?php if (0 === count($logInvite)): ?>
        <div style="text-align: center; width: 100%;">Нет записей.</div>
    <?php endif; ?>

<hr/>

    <!-- Simulation: -->

    <?php $this->renderPartial('//admin_area/pages/simulation_site_logs_table', [
        'logSimulation'    => $logSimulation,
    ]) ?>
