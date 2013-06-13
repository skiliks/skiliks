<?php

/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 05.02.13
 * Time: 12:14
 * To change this template use File | Settings | File Templates.
 */
$cs = Yii::app()->clientScript;
$assetsUrl = $this->getAssetsUrl();
$cs->registerCssFile($assetsUrl . "/css/style_new.css");
?>

<!DOCTYPE html>
<html lang="<?php echo Yii::t('site', 'en') ?>">
<head>
    <meta property="og:image" content="<?php echo $assetsUrl?>/img/skiliks-fb.png"/>
    <meta charset="utf-8" />
    <meta name="description" content="Самый простой и надежный способ проверить навыки менеджеров:
        деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами
        и ситуациями принятия решений">
    <meta property="og:description" content="Самый простой и надежный способ проверить навыки менеджеров:
        деловая онлайн симуляция, имитирующая реальный рабочий день с типичными управленческими задачами
        и ситуациями принятия решений">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <title>Skiliks - game the skills</title>

    <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
<style>


</style>
</head>

<body>
<a class="btn-large" href="#">Получить</a>
</body>
</html>
