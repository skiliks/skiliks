<!DOCTYPE html>
<html lang="{Yii::t('site', 'en')}">
	<head>
		<meta charset="utf-8" />
        <link href="../../../favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<title>{Yii::t('site', 'Skiliks - game the skills')}</title>

		<!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
    </head>

	<body>
		<div class="container main-page" id="top">
			
			<!--header-->
			<header>
				<h1><a href="/">Skiliks</a></h1>
				
				<p class="coming-soon">{Yii::t('site', 'Coming soon')}</p>
				
				<div class="language"><a href="#">{Yii::t('site', 'Русский')}</a></div>
				
				<nav>
					<a href="#">{Yii::t('site', 'Home')}</a>
					<a href="#">{Yii::t('site', 'About')}</a>
					<a href="#">{Yii::t('site', 'Product')}</a>
				</nav>
			</header>
			<!--header end-->
			
			<!--content-->
			<div class="content">
				
				<!--features-->
				<section class="features">
					<h2>{Yii::t('site', 'The easiest &amp; most reliable way to discover your people management skills!')}</h2>
					
					<div class="video">
						<p>{Yii::t('site', 'Coming soon')}</p>
					</div>
					
					<ul>
						<li>{Yii::t('site', 'Simulation aimed at testing manager’s skills')}</li>
						<li>{Yii::t('site', '2-3-hours game')}</li>
						<li>{Yii::t('site', 'Live tasks and decision-making situations')}</li>
						<li>{Yii::t('site', 'A tool to assess candidates and newcomers')}</li>
					</ul>
					
                    <!-- FORM { -->
                    <form action="/sub/add" id="subscribe-form">
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
						<h3>{Yii::t('site', 'Most Reliable')}</h3>
						<ul>
							<li>{Yii::t('site', 'Focused on key skills')}</li>
							<li>{Yii::t('site', 'Based on best working practices')}</li>
							<li>{Yii::t('site', 'Uses real work environment, tasks and decision making situations')}</li>
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
				
			</div>
			<!--content end-->
		</div>
		
		<!--footer-->
		<div class="footer">
			<footer>
				<div class="backtotop"><a href="#top">{Yii::t('site', 'Back to top')}</a></div>
				
				<div class="logo"><a href="/">Skiliks</a></div>
				
				<nav>
					<a href="#">{Yii::t('site', 'Home')}</a>
					<a href="#">{Yii::t('site', 'About')}</a>
					<a href="#">{Yii::t('site', 'Product')}</a>
				</nav>
				
				<p class="copyright">Copyright - Skiliks  - 2012</p>
			</footer>
		</div>
		<!--footer end-->
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
                                    window.location.href = '/site/comingSoonSuccess';
                                } else {
                                    // invalid email
                                    console.log('e1');
                                    displayError(response.message);
                                }
                            } else {
                                console.log('e2');
                                // wrong server response format
                                displayError("{Yii::t('site', 'No proper response from server. Please try again later.')}");
                            }
                        },
                        error: function() {
                            console.log('e3');
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
                console.log($('#user-email-error-box').height());
                $('#user-email-error-box').css('top', '-' + ($('#user-email-error-box').height()) + 'px');
                $('#user-email-error-box').fadeIn(1000);
            }
    
            hideError = function() {
                $('#user-email-error-box').fadeOut(1000);
                $('#user-email-error-box span').text('');        
            }
        </script>
        {/literal}
	</body>
</html>