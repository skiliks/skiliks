				<!--features-->
				<section class="features">
					<h2>{Yii::t('site', 'Easy &amp; reliable way to discover your people management skills!')}</h2>
					
					<div class="video">
                        <iframe src="http://player.vimeo.com/video/{Yii::t('site', '61258856')}" width="396" height="211" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
					</div>
					
					<ul>
						<li>{Yii::t('site', 'Simulation aimed at testing manager’s skills')}</li>
						<li>{Yii::t('site', '2-hours game')}</li>
						<li>{Yii::t('site', 'Live tasks and decision-making situations')}</li>
						<li>{Yii::t('site', 'A tool to assess candidates and employees')}</li>
					</ul>
					
                    <!-- FORM { -->
                    <form action="subscription/add" id="subscribe-form">
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
                        <li>{CHtml::image("$assetsUrl/img/icon-hipway.png")}</li>
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