
<?php
$assetsUrl = $this->getAssetsUrl();
?>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script src="<?=$assetsUrl?>/js/jquery/popover.js"></script>
<a id="my-button" style="margin-left:500px;">Popover Link</a>

<script>
    $(document).ready(function() {
        $('#my-button').popover({content: '#my-popover > .popup-content'});
    });
</script>

<div id="my-popover">
    <div class="popup-content">
        aaaaaaaaaaaaa
    </div>
</div>