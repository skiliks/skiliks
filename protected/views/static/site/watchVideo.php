<?php
$vimeoVideoId = '61279471';

if ('en' == Yii::app()->language) {
    $vimeoVideoId = '61258856';
}

// ?title=0&amp;byline=0&amp;portrait=0&amp;color=24bdd3
?>

<div id="error404-message">
    <br/>
    <iframe src="//player.vimeo.com/video/<?= $vimeoVideoId; ?>"
        width="100%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
</div>
<script>
    $("iframe").attr("height", $("iframe").width()/1.777777);
</script>