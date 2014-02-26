<div class="locator-simulation-details-popup"></div>
<?php if (null != $display_results_for): ?>
    <script type="text/javascript">
        $(function() {
            showSimulationDetails('/simulation/<?= $display_results_for->id ?>/details');
        });
    </script>
<?php endif ?>