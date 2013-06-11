<!DOCTYPE html>
<html lang="ru">
<head>
    <base href="<? Yii::app()->request->pathInfo ?>">
    <meta charset="utf-8">
    <title>Template &middot; Bootstrap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="/public/admin_area/css/main.css" rel="stylesheet">
    <link href="/public/admin_area/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="/public/admin_area/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
    <script src="/public/admin_area/js/jquery-2.0.2.js"></script>
    <script src="/public/admin_area/bootstrap/js/bootstrap.js"></script>
    <script src="/public/admin_area/js/main.js"></script>
</head>
<body>
    <div><?=$content?></div>
</body>
</html>
