
<html>
<head>
    <?php Yii::app()->clientScript->registerCssFile($this->getAssetsUrl() . "/css/statistics/ci.css?dl=1"); ?>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="60">
</head>
<body>
    <div id="status"><?= $count ?> (<?= $count_today ?>) отзывов на <?= Yii::app()->request->hostInfo ?></div>
</body>
