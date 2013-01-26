<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
        <link href="../../../favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<title>Skiliks - game the skills</title>

		<!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
    </head>

	<body>
		<div class="container main-page" id="top">
			
			<!--header-->
			<header>
				<h1><a href="/">Skiliks</a></h1>
				
				<p class="coming-soon">Coming soon</p>
			</header>
			<!--header end-->
			
			<!--content-->
			<div class="content">
				
				<!--features-->
				<section class="features">
					<h2>The easiest &amp; most reliable way to discover your people management skills!</h2>
					
					<div class="video">
						{CHtml::image("$assetsUrl/img/img-video.png")}
					</div>
					
					<ul>
						<li>Simulation aimed at testing manager’s skills</li>
						<li>2-3-hours game</li>
						<li>Live tasks and decision-making situations</li>
						<li>A tool to assess candidates and newcomers</li>
					</ul>
					
                    <!-- FORM { -->
                    <form action="/sub/add" id="subscribe-form">
                        <div>
                            <input type="text" 
                                   id = "user-email-value"
                                   placeholder="Enter your email address"
                                   />
                            <p id="user-email-error-box" class="error" style="display: none;">
                                <span>Please enter a valid email address</span>
                            </p>
                        </div>
                        <div><input type="submit" value="" /></div>
                    </form>
                    <!-- FORM } -->
				</section>
				<!--features end-->
				
				<!--main article-->
				<section class="main-article">
					<article>
						<h3>Easiest</h3>
						<ul>
							<li>Saves your time</li>
							<li>Can be used by an unlimited number of applicants in any part of the world</li>
							<li>No hard-, soft- or any-ware required! ! Just make sure you are online</li>
							<li>Results can be obtained and used immediately</li>
						</ul>
					</article>
					
					<article>
						<h3>Most Reliable</h3>
						<ul>
							<li>Focused on key skills</li>
							<li>Based on bestworking practices</li>
							<li>Uses real work environment, tasks and decision making situations</li>
							<li>Based on mathematical methods not just feelings</li>
						</ul>
					</article>
				</section>
				<!--main article end-->
				
				<!--clients-->
				<section class="clients">
					<h3>Our Clients</h3>
					
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
				<div class="backtotop"><a href="#top">Back to top</a></div>
				
				<div class="logo"><a href="/">Skiliks</a></div>
				
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
                                    window.location.href = '/coming-soon-success.html';
                                } else {
                                    // invalid email
                                    console.log('e1');
                                    displayError(response.message);
                                }
                            } else {
                                console.log('e2');
                                // wrong server response format
                                displayError('No proper response from server. Please try again later.');
                            }
                        },
                        error: function() {
                            console.log('e3');
                            // no response from server
                            displayError('No response from server. Please try again later.');
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