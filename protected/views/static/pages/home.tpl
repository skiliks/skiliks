<script>
    $(document).ready(function(){
        var iframesrc = $(".iframe-video iframe").attr("src");
        var iframesrcautoplay = iframesrc +'?autoplay=1';

        var popupwidth = $("header").width() * 0.9;
        var video = $(".iframe-video-wrap").html();


        $(".video").click(function(){
            $(video).dialog({
                modal: true,
                resizable: false,
                height: 354,
                width: popupwidth,
                dialogClass:"popup-video",
                position: {
                    my: "center top",
                    at: "center bottom",
                    of: $('header')
                },
                show: {
                    effect: "clip",
                    duration: 1000
                },
                hide: {
                    effect: "puff",
                    duration: 500
                }
            });
            $(".popup-video .iframe-video iframe").attr("src",iframesrcautoplay);
            $('.popup-video .ui-dialog-titlebar').remove();
            $('.popup-video').prepend('<a class="popupclose" href="javascript:void(0);"></a>');
            $('.popup-video a.popupclose').click(function() {
                $('.iframe-video').dialog('close');
                $('.popup-video a.popupclose').remove();
                $('.iframe-video').detach();
            });

        });
    })

</script>
<!--features-->
				<section class="features">
					<h2>{Yii::t('site', 'Easy &amp; reliable way to discover your people management skills!')}</h2>
                    <div class="iframe-video-wrap">
                        <div class="iframe-video">
                            <iframe src="http://player.vimeo.com/video/{Yii::t('site', '61258856')}" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                        </div>
                    </div>
                    <div class="videosocwrap clearfix">
                        <div class="video" style="cursor: pointer;">
                            <span class="video-caption">{Yii::t('site', 'Watch the video to learn more')}</span>
                        </div>
                        <div class="social_networks smallicons">
                            <span>{Yii::t('site', 'Share video')}:</span>
                            <div class="addthis_toolbox addthis_default_style addthis_32x32_style"
                                 addthis:url="http://player.vimeo.com/video/{Yii::t('site', '61258856')}?title=0&amp;byline=0&amp;portrait=0&amp;color=24bdd3"
                                 addthis:title="Skiliks - game the skills"
                                 addthis:description="{Yii::t('site', 'www.skiliks.com - online simulation aimed at testing management skills')}">

                                <a class="new_social_buttons vk_share_button" title="VK" onclick="
                                        window.open(
                                        'http://vk.com/share.php?url=' + encodeURIComponent('{Yii::app()->request->hostInfo}' +'/watchVideo'),
                                        'vk-share-dialog',
                                        'width=626,height=436');
                                        return false;" href="#" target="_blank">
                                </a>

                                <a class="new_social_buttons facebook_share_button" title="Facebook" onclick="
                                    window.open(
                                      'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent('{Yii::app()->request->hostInfo}' +'/watchVideo'),
                                      'facebook-share-dialog',
                                      'width=626,height=436');
                                    return false;" href="#" target="_blank">
                                </a>


                                <a class="new_social_buttons twitter_share_button" title="Twitter" onclick="
                                        window.open(
                                        'https://twitter.com/share?url=' + encodeURIComponent('{Yii::app()->request->hostInfo}' +'/watchVideo'),
                                        'twitter-share-dialog',
                                        'width=626,height=436');
                                        return false;" href="#" target="_blank">
                                </a>

                                <a class="new_social_buttons google_share_button" title="Google" onclick="
                                    window.open(
                                    'https://plus.google.com/share?url=' + encodeURIComponent('{Yii::app()->request->hostInfo}' +'/watchVideo'),
                                    'google-share-dialog',
                                    'width=626,height=436');
                                    return false;" href="#" target="_blank">
                                </a>

                                <a class="new_social_buttons linkedin_share_button" title="Linkedin" onclick="
                                        window.open(
                                        'https://www.linkedin.com/cws/share?url=' + encodeURIComponent('{Yii::app()->request->hostInfo}' +'/watchVideo'),
                                        'linkedin-share-dialog',
                                        'width=626,height=436');
                                        return false;" href="#" target="_blank">
                                </a>
                            </div>
                        </div>
                    </div>
					<ul>
						<li>{Yii::t('site', 'Simulation aimed at testing basic manager’s skills')}</li>
						<li>{Yii::t('site', '2-3-hours game')}</li>
						<li>{Yii::t('site', 'Live tasks and decision-making situations')}</li>
						<li>{Yii::t('site', 'A tool to assess candidates and newcomers')}</li>
					</ul>

                    {if ('ru' == Yii::app()->getlanguage()) }

                        <a href="/registration" class="bigbtnsubmt freeacess">{Yii::t('site', 'Start using it now for free')}</a>

                    {elseif ('en' == Yii::app()->getlanguage()) }
                            <!-- FORM { -->
                            <div id="notify-form">
                            <form action="static/pages/addUserSubscription" id="subscribe-form">
                                <div>
                                    <input type="text"
                                           id = "user-email-value"
                                           placeholder="{Yii::t('site', 'Enter your email address')}"
                                           />
                                    <p id="user-email-error-box" class="errorMessage" style="display: none; top:-17px; left:2px; white-space: nowrap;">
                                        {Yii::t('site', 'Please enter a valid email address')}
                                    </p>
                                </div>
                                <div><input type="submit" value="{Yii::t('site', 'Notify me')}" /></div>
                            </form>
                            </div>
                        <!-- FORM } -->
                    {/if}

				</section>
				<!--features end-->
				
				<!--main article-->
				<section class="main-article">
					<article>
						<h3>{Yii::t('site', 'Easy')}</h3>
						<ul>
							<li>{Yii::t('site', 'Saves your time')}</li>
							<li>{Yii::t('site', 'Can be used by an unlimited number of applicants in any part of the world')}</li>
							<li>{Yii::t('site', 'No hard-, soft- or any-ware required! ! Just make sure you are online')}</li>
							<li>{Yii::t('site', 'Results can be obtained and used immediately')}</li>
						</ul>
					</article>
					
					<article>
						<h3>{Yii::t('site', 'Reliable')}</h3>
						<ul>
							<li>{Yii::t('site', 'Focused on key practical skills')}</li>
							<li>{Yii::t('site', 'Based on best working practices')}</li>
							<li>{Yii::t('site', 'Uses real work environment, tasks and decision<br />making situations')}</li>
							<li>{Yii::t('site', 'Based on mathematical methods not just feelings')}</li>
						</ul>
					</article>
				</section>
				<!--main article end-->
				
				<!--clients-->
				<section class="clients">
					<h3>{Yii::t('site', 'Our Clients')}</h3>
					
					<ul>
                        <li style="display:none;">{CHtml::image("$assetsUrl/img/skiliks-fb.png")}</li>
                        <li>{CHtml::image("$assetsUrl/img/icon-hipway.png")}</a></li>
						<li>{CHtml::image("$assetsUrl/img/icon-mif.png")}</li>
						<li>{CHtml::image("$assetsUrl/img/icon-wikimart.png")}</li>
						<li>{CHtml::image("$assetsUrl/img/icon-mcg.png")}</li>
					</ul>
				</section>
				<!--clients end-->
		
        {literal}
        <script type="text/javascript">
            $(document).ready(function(){
                $('#subscribe-form').submit(function(e) {
                    hideError();
                    e.preventDefault();
            
                    $.ajax({
                        url: $(this).attr('action'),
                        type: 'POST',
                        data: {'email': $('#user-email-value').val()},
                        success: function(response) {
                            if ('undefined' !== typeof response.result || 'undefined' !== typeof response.message) {
                                if (1 === response.result) {
                                    // redirect to success page
                                    $('#notify-form').html('<p class="success">Thank you! See you soon</p>');
                                    //window.location.href = '/static/comingSoonSuccess/en';
                                    $.cookie('_lang', 'en'); //установить значение cookie
                                } else {
                                    // invalid email
                                    displayError(response.message);
                                }
                            } else {
                                // wrong server response format
                                displayError("No proper response from server. Please try again later.");
                            }
                        },
                        error: function() {
                            // no response from server
                            displayError("No response from server. Please try again later.");
                        }                
                    });
            
                    // prevent default behaviour
                    return true;
                });
            });
    
            displayError = function(msg) {
                $('#user-email-error-box').text(msg);
                //$('#user-email-error-box').css('top', '-' + ($('#user-email-error-box').height()) + 'px');
                $('#user-email-error-box').show();
                $('#user-email-value').css({"border":"2px solid #BD2929","margin-top":"-2px"});
            }

            hideError = function() {
                $('#user-email-error-box').hide();
                $('#user-email-error-box').text('');
                $('#user-email-value').css({"border":"none","margin-top":"0"});
            }
        </script>
        {/literal}