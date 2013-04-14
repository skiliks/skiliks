				<!--features-->
				<section class="features">
					<h2>{Yii::t('site', 'The easiest &amp; most reliable way to discover your people management skills!')}</h2>

                    <div class="videosocwrap clearfix">
                        <div class="video">
                            <iframe src="http://player.vimeo.com/video/{Yii::t('site', '61258856')}" width="396" height="211" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
                        </div>
                        <div class="social_networks smallicons">
                            <span>Рекомендовать:</span>
                            <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                                <a class="addthis_button_vk at300b" target="_blank" title="Vk" href="#"><span class=" at300bs at15nc at15t_vk"><span class="at_a11y">Share on vk</span></span></a>
                                <a class="addthis_button_facebook at300b" title="Facebook" href="#"><span class=" at300bs at15nc at15t_facebook"><span class="at_a11y">Share on facebook</span></span></a>
                                <a class="addthis_button_twitter at300b" title="Tweet" href="#"><span class=" at300bs at15nc at15t_twitter"><span class="at_a11y">Share on twitter</span></span></a>
                                <a class="addthis_button_google_plusone_share at300b" g:plusone:count="false" href="http://www.addthis.com/bookmark.php?v=300&amp;winname=addthis&amp;pub=ra-5158c9c22198d938&amp;source=tbx32-300&amp;lng=en-US&amp;s=google_plusone_share&amp;url=http%3A%2F%2Flive.skiliks.com%2F&amp;title=Skiliks%20-%20game%20the%20skills&amp;ate=AT-ra-5158c9c22198d938/-/-/516aff725e2802ec/2&amp;frommenu=1&amp;uid=516aff72dd444ad3&amp;ct=1&amp;pre=http%3A%2F%2Flive.skiliks.com%2Fdashboard&amp;tt=0&amp;captcha_provider=nucaptcha" target="_blank" title="Google+"><span class=" at300bs at15nc at15t_google_plusone_share"><span class="at_a11y">Share on google_plusone_share</span></span></a>
                                <a class="addthis_button_linkedin at300b" href="http://www.addthis.com/bookmark.php?v=300&amp;winname=addthis&amp;pub=ra-5158c9c22198d938&amp;source=tbx32-300&amp;lng=en-US&amp;s=linkedin&amp;url=http%3A%2F%2Flive.skiliks.com%2F&amp;title=Skiliks%20-%20game%20the%20skills&amp;ate=AT-ra-5158c9c22198d938/-/-/516aff725e2802ec/3&amp;frommenu=1&amp;uid=516aff725adc8e8d&amp;ct=1&amp;pre=http%3A%2F%2Flive.skiliks.com%2Fdashboard&amp;tt=0&amp;captcha_provider=nucaptcha" target="_blank" title="Linkedin"><span class=" at300bs at15nc at15t_linkedin"><span class="at_a11y">Share on linkedin</span></span></a>
                                <div class="atclear"></div></div>
                        </div>
                    </div>
					<ul>
						<li>{Yii::t('site', 'Simulation aimed at testing manager’s skills')}</li>
						<li>{Yii::t('site', '2-3-hours game')}</li>
						<li>{Yii::t('site', 'Live tasks and decision-making situations')}</li>
						<li>{Yii::t('site', 'A tool to assess candidates and newcomers')}</li>
					</ul>

                    <a href="/registration" class="bigbtnsubmt freeacess">{Yii::t('site', 'Get free access')}</a>
                    <!-- FORM { -->
                    <!--
                    {if (false === $userSubscribed) }

                        <form action="static/pages/addUserSubscription" id="subscribe-form">
                            <div>
                                <input type="text"
                                       id = "user-email-value"
                                       placeholder="{Yii::t('site', 'Enter your email address')}"
                                       />
                                <p id="user-email-error-box" class="error" style="display: none;">
                                    <span>{Yii::t('site', 'Please enter a valid email address')}</span>
                                </p>
                            </div>
                            <div><input type="submit" value="{Yii::t('site', 'Notify me')}" /></div>
                        </form>

                    {else}
                        <p class="success">{Yii::t('site', 'Thank you! See you soon!')}</p>
                    {/if}

                    -->
                    <!-- FORM } -->

				</section>
				<!--features end-->
				
				<!--main article-->
				<section class="main-article">
					<article>
						<h3>{Yii::t('site', 'Easiest')}</h3>
						<ul>
							<li>{Yii::t('site', 'Saves your time')}</li>
							<li>{Yii::t('site', 'Can be used by an unlimited number of applicants in any part of the world')}</li>
							<li>{Yii::t('site', 'No hard-, soft- or any-ware required! ! Just make sure you are online')}</li>
							<li>{Yii::t('site', 'Results can be obtained and used immediately')}</li>
						</ul>
					</article>
					
					<article>
						<h3>{Yii::t('site', 'Most Reliable')}</h3>
						<ul>
							<li>{Yii::t('site', 'Focused on key skills')}</li>
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
                        <li><a href="#">{CHtml::image("$assetsUrl/img/icon-hipway.png")}</a></li>
						<li><a href="#" style="margin-top:12px;">{CHtml::image("$assetsUrl/img/icon-mif.png")}</a></li>
						<li><a href="#" style="margin-top:8px;">{CHtml::image("$assetsUrl/img/icon-wikimart.png")}</a></li>
						<li><a href="#">{CHtml::image("$assetsUrl/img/icon-mcg.png")}</a></li>
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
                                    window.location.href = '/static/comingSoonSuccess';
                                } else {
                                    // invalid email
                                    displayError(response.message);
                                }
                            } else {
                                // wrong server response format
                                displayError("{Yii::t('site', 'No proper response from server. Please try again later.')}");
                            }
                        },
                        error: function() {
                            // no response from server
                            displayError("{Yii::t('site', 'No response from server. Please try again later.')}");
                        }                
                    });
            
                    // prevent default behaviour
                    return true;
                });
            });
    
            displayError = function(msg) {
                $('#user-email-error-box span').text(msg);
                $('#user-email-error-box').css('top', '-' + ($('#user-email-error-box').height()) + 'px');
                $('#user-email-error-box').fadeIn(1000);
            }
    
            hideError = function() {
                $('#user-email-error-box').fadeOut(1000);
                $('#user-email-error-box span').text('');        
            }
        </script>
        {/literal}