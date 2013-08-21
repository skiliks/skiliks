<h2>Мои навыки</h2>

<?php
    if (null !== $simulation && null !== $invite && true === $simulation->invite->isAllowedToSeeResults(Yii::app()->user->data())) {
        $this->renderPartial('//global_partials/_simulation_stars', [
            'simulation'    => $simulation,
        ]);
    } else {
        echo '<br/>';
    }
?>

<?php /* not in release 1
    <div>
        <a href="#" class="light-btn">Compare</a>
        <a href="#" class="light-btn">Apply to position</a>
    </div>
*/?>