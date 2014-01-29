
<?php
    $flashes = Yii::app()->user->getFlashes();

    $info = [];
    foreach ($flashes as $key => $message) {
        if ('error' == $key || 'success' == $key) {
            $info[] = [
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

