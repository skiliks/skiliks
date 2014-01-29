<?php

    $flashes = Yii::app()->user->getFlashes();

    $info = [];
    $passwordRecovery = null;

    foreach ($flashes as $key => $message) {
        if ('error' == $key || 'success' == $key || 'notice' == $key) {
            $info[] = [
                'key'     => $key,
                'message' => $message
            ];
        }

        if ('password-recovery' == $key) {
            $passwordRecovery = [
                'key'     => $key,
                'message' => $message
            ];
        }
    }
?>

<?php if (0 < count($info)) : ?>
    <div class="locator-flash hide">
        <?php foreach($info as $infoMessage) : ?>
            <div class="flash-data flash-<?php echo $infoMessage['key'] ?>">
                <?php echo $infoMessage['message'] ?>
            </div>
        <?php endforeach ?>
    </div>
<?php endif; ?>

<?php // password-recovery ?>

<?php if (null != $passwordRecovery) : ?>
    <div class="locator-password-recovery-success hide">
        <div class="flash-data flash-<?php echo $passwordRecovery['key'] ?>">
            <?php echo $passwordRecovery['message'] ?>
        </div>
    </div>
<?php endif; ?>

