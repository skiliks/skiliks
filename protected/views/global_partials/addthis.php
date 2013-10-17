<?php
    $allow = ['ru', 'en', '', 'static/team',
        'static/team/ru', 'static/team/en', 'static/product',
        'static/product/ru', 'static/product/en'];
?>
<?php if($force || in_array(Yii::app()->request->getPathInfo(), $allow)){ ?>
    <div class="social_networks"><span><?php echo Yii::t('site', "Share");?>:</span><div class="addthis_toolbox addthis_default_style addthis_32x32_style"><a class="addthis_button_vk"></a>
        <a class="new_social_buttons_footer facebook_share_button_footer" title="Facebook" onclick="
    window.open(
      'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent('<?=Yii::app()->request->hostInfo?>'),
      'facebook-share-dialog',
      'width=626,height=436');
    return false;" href="#" target="_blank"></a>
        <a class="addthis_button_twitter"></a>


        <a class="new_social_buttons_footer google_share_button_footer" title="Google" onclick="
            window.open(
            'https://plus.google.com/share?url=' + encodeURIComponent('<?=Yii::app()->request->hostInfo?>'),
            'facebook-share-dialog',
            'width=626,height=436');
            return false;" href="#" target="_blank">
        </a>


        <a class="addthis_button_linkedin"></a>
    </div>
</div>
<script type="text/javascript">var addthis_config = {"data_track_addressbar":true};</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5158c9c22198d938"></script>
<!-- AddThis Button END -->
<?php } ?>