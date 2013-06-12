<? foreach(Yii::app()->user->getFlashes() as $class => $message) : ?>
    <div class="alert alert-<?= $class ?>">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= $message ?>
    </div>
<? endforeach ?>