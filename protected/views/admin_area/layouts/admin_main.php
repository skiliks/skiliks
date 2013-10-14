<!DOCTYPE html>
<html lang="ru">
<head>
    <base href="<?php Yii::app()->request->pathInfo ?>">
    <meta charset="utf-8">
    <title>Template &middot; Bootstrap</title>
    <link href="/public/admin_area/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="/public/admin_area/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <link href="/public/admin_area/css/main.css" rel="stylesheet">

    <script src="/public/admin_area/js/jquery-2.0.2.js"></script>
    <script src="http://code.jquery.com/jquery-migrate-1.2.1.js"></script>
    <script src="/public/admin_area/js/jquery.ba-bbq.min.js"></script>
    <script src="/public/admin_area/bootstrap/js/bootstrap.js"></script>
    <script src="/public/admin_area/js/jquery.scrollTo.js"></script>
    <script src="/public/admin_area/js/main.js"></script>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container-fluid">
            <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="brand" href="/">Skiliks: release 2.1.0 (internal release, 2013-oct-14 16:55 Kiev time)</a>
            <?php $this->renderPartial('//admin_area/partials/_top_menu') ?>
        </div>
    </div>
</div>

<br/><br/>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span2">
            <?php $this->renderPartial('//admin_area/partials/_left_menu', []) ?>
        </div>
        <div class="span10">

            <br/>

            <?php foreach (Yii::app()->user->getFlashes() as $class => $message): ?>
                <div class="alert alert-<?= $class ?>">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= $message ?>
                </div>
            <?php endforeach ?>

            <?= $content?>
        </div>
    </div>

    <hr>

    <footer>
        <p>Yii version: <?= Yii::getVersion(); ?></p>
    </footer>
</div>
</body>
</html>
