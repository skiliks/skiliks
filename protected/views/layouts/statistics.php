<html>
<head>
    <meta charset="utf-8">

    <?php Yii::app()->clientScript->registerCssFile($this->getAssetsUrl() . "/css/statistics/ci.css?dl=1"); ?>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
</head>
<body>
    <?php echo $content; ?>
</body>