
<p><span class="skillstitle">Базовый менеджмент</span>
    <span <?php if(null!==$simulation) { echo 'data-simulation="/simulations/details/'.$simulation->id.'"';} ?> class="ratingwrap radiusthree <?php if(null!==$simulation) { echo "view-simulation-details-pop-up";} ?>">
        <span class="ratebg"><span class="rating" style="width: <?php if(null!==$simulation){ echo $simulation->overall_manager_rating; }else{ echo "0"; } ?>%"></span></span>
        <sup><?php if(null!==$simulation){ echo $simulation->overall_manager_rating; }else{ echo "0"; } ?>%</sup>
    </span>
    <?php if(null!==$simulation) { ?>
        <a href="#" data-simulation="/simulations/details/<?php echo $simulation->id; ?>" class="link-go view-simulation-details-pop-up"></a>
    <?php } ?>
</p>