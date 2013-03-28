<?php
    $isDisplayLink = (isset($isDisplayLink)) ? true : false ;
    $value = (isset($value)) ? $value : '--' ;
    $label = (isset($label)) ? 'Менеджмент' : '--' ;
?>

<p>Менеджмент
    <span class="ratingwrap radiusthree">
        <span class="ratebg"><span class="rating" style="width: <?php echo $value; ?>%"></span></span>
        <sup><?php echo $value; ?>%</sup>
    </span>
    <a href="#" class="link-go"></a>
</p>