<?php /* @var Simulation $display_results_for */ ?>

<?php if (null != $display_results_for): ?>

    <?php /* @var Invite $invite */ ?>
    <?php $invite = Invite::model()->findByAttributes(['simulation_id' => $display_results_for->id]) ?>

    <?php if (null != $invite && 1 == $invite->is_display_simulation_results ): ?>

        <script type="text/javascript">
            window.display_results_for = <?= $display_results_for->id ?>;
        </script>
    <?php endif ?>
<?php endif ?>