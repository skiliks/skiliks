<!DOCTYPE html>
<html lang="ru">
<head>
    <base href="<? Yii::app()->request->pathInfo ?>">
    <meta charset="utf-8">
    <title>Template &middot; Bootstrap</title>
    <link href="/public/admin_area/css/main.css" rel="stylesheet">
    <link href="/public/admin_area/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="/public/admin_area/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <script src="/public/admin_area/js/jquery-2.0.2.js"></script>
    <script src="/public/admin_area/bootstrap/js/bootstrap.js"></script>
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
            <a class="brand" href="#">Skiliks</a>
                <? $this->renderPartial('//admin_area/main_menu', []) ?>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row-fluid">
        <div class="span3">
            <? $this->renderPartial('//admin_area/sidebar', []) ?>
        </div><!--/span-->
        <div class="span9">
            <?=$content?>
        </div><!--/span-->
    </div><!--/row-->

    <hr>
    <? $this->renderPartial('//admin_area/footer', []) ?>
</div><!--/.fluid-container-->
</body>
</html>
