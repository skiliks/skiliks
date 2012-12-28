<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
        <link href="../../../favicon.ico" rel="shortcut icon" type="image/x-icon" />
		<title>Skiliks - game the skills</title>

		<!--[if IE]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

		<link rel="stylesheet" href="/static/css/style.css" />
        <script type="text/javascript" src="/static/js/jquery/jquery-1.7.2.min.js"></script>
	</head>

	<body>
		<div class="container main-page" id="top">
			
			<!--header-->
			<header>
				<h1><a href="#">Skiliks</a></h1>
				
				<p class="coming-soon">Coming soon</p>
			</header>
			<!--header end-->
			
			<!--content-->
			<div class="content">
				
				<!--features-->
				<section class="features">
					<h2>The easiest &amp; most reliable way to discover your people management skills!</h2>
					
					<div class="video">
						<img src="/static/img/img-video.png" alt="" />
					</div>
					
					<ul>
						<li>Simulation aimed at testing manager’s skills</li>
						<li>2-3-hours game</li>
						<li>Live tasks and decision-making situations</li>
						<li>A tool to assess candidates and newcomers</li>
					</ul>
					
                    <!-- FORM { -->
                    <form action="/api.php/Sub/Add" id="subscribe-form">
                        <div>
                            <input type="text" 
                                   id = "user-email-value"
                                   value   = "Enter your email address" 
                                   onblur  = "if (value == '') { value = 'Enter your email address'; };" 
                                   onfocus = "if (value == 'Enter your email address') { value =''; };" 
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
						<li><a href="#"><img src="img/icon-hipway.png" alt="" /></a></li>
						<li><a href="#" style="margin-top:12px;"><img src="img/icon-mif.png" alt="" /></a></li>
						<li><a href="#" style="margin-top:8px;"><img src="img/icon-wikimart.png" alt="" /></a></li>
						<li><a href="#"><img src="img/icon-mcg.png" alt="" /></a></li>
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
                                    // redirect to succes page
                                    window.location.href = '/coming-soon-success.html';
                                } else {
                                    // invalid email
                                    console.log('e1');
                                    displayError(response.message);
                                }
                            } else {
                                console.log('e2');
                                // wrong server response format
                                displayError('No proper response from server. Please try again several seconds ago.');
                            }
                        },
                        error: function() {
                            console.log('e3');
                            // no response from server
                            displayError('No response from server. Please try again several seconds ago.');
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