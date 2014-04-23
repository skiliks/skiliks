<?php /* @var Simulation $display_results_for */ ?>

<?php if (null != $display_results_for): ?>
    <?php /* @var Invite $invite */ ?>
    <?php $invite = Invite::model()->findByAttributes(['simulation_id' => $display_results_for->id]) ?>

    <?php if ($display_results_for->isAllowedToSeeResults(Yii::app()->user->data())): ?>

        <script type="text/javascript">
            window.display_results_for = <?= $display_results_for->id ?>;
        </script>
    <?php endif ?>
<?php endif ?>