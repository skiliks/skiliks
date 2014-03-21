<?php
$vimeoVideoId = '61258856';

if ('ru' == Yii::app()->language) {
    $vimeoVideoId = '61279471';
}
?>

<br/>
<br/>
<br/>
<br/>

<div class="pull-content-center">

    <iframe src="//player.vimeo.com/video/<?= $vimeoVideoId; ?>"
            class="nice-border reset-padding border-radius-standard"
        width="960" height="535" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen>
    </iframe>
</div>

<div class="clearfix"></div>