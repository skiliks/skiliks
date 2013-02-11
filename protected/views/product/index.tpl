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

	<body class="inner">
		<div class="container" id="top">
			
			<!--header-->
			<header>
				<h1><a href="/">Skiliks</a></h1>
				
				<div class="language"><a href="?_lang={Yii::t('site', 'ru')}">{Yii::t('site', 'Русский')}</a></div>
				
				<nav>
					<a href="../">{Yii::t('site', 'Home')}</a>
					<a href="#">{Yii::t('site', 'About')}</a>
					<a href="product" class="active">{Yii::t('site', 'Product')}</a>
				</nav>
			</header>
			<!--header end-->
			
			<!--content-->
			<div class="content">
				
				<!--product-->
				<article class="product">
					<hgroup>
						<h2>{Yii::t('site', 'About the Product')}</h2>
						<h6>{Yii::t('site', 'We have created on-line business simulation to discover manager’s skills.')}</h6>
					</hgroup>
					
					<table>
						<col />
						<col />
						<col />
						<tr>
							<td>{Yii::t('site', 'Assessor needs no more than 5 minutes to appoint the assessment session and get the results. Detailed and comprehensive report on productivity, demonsrated managerial skills and professional qualities will be provided for each assessed candidate.')}</td>
							<td>{Yii::t('site', 'Applicant needs 2-3 hours to get  through the exciting gameplay allowing a deep immersion into real working environment with managerial tasks and decision-making situations.')}</td>
							<td>{Yii::t('site', 'Assessment outcome of a particular person can be compared with the outcomes of other people with different backgrounds, geographies, etc. This option is available for either assessors or applicants.')}</td>
						</tr>
					</table>
					
					<section>
						<hgroup>
							<h3>{Yii::t('site', 'More Information')}</h3>
							<h6>{Yii::t('site', 'Our simulation is the easiest and most reliable way to discover your people management skills:')}</h6>
						</hgroup>
						
						<table>
							<col />
							<col />
							<tr>
								<th><h5>{Yii::t('site', 'Easiest')}</h5></th>
								<th><h5>{Yii::t('site', 'Most Reliable')}</h5></th>
							</tr>
							<tr>
								<td>
									<h6>{Yii::t('site', 'Saves your time')}</h6>
									<p>{Yii::t('site', 'Add up the number of hours you waste on the futile  interviews. Use these hours on your first priority project!')}</p>
									<p>{Yii::t('site', 'Two clicks to start the process and get the necessary assessment.')}</p>
									<h6>{Yii::t('site', 'Unlimited number of applicants in any part of the world.')}</h6>
									<p>{Yii::t('site', 'No limits! Use the simulation for managers from anywhere in the world. Assess as many people as you need.')}</p>
									<h6>{Yii::t('site', 'No hard-, soft- or any-ware required! Just make  sure you and your managers are on line!')}</h6>
									<p>{Yii::t('site', '<strong>NO NEED</strong> to  buy computers')}</p>
									<p>{Yii::t('site', '<strong>NO NEED</strong> to buy/distribute/integrate any soft')}</p>
									<p>{Yii::t('site', '<strong>NO NEED</strong> to lease additional office space')}</p>
									<h6>{Yii::t('site', 'Results can be used immediately')}</h6>
									<p>{Yii::t('site', '<strong>NO NEED</strong> to call experts to interpret the results! Just open your Skiliks Office and use the managers’ assessment data to make the decision!')}</p>
								</td>
								<td>
									<h6>{Yii::t('site', 'Focused on the skills')}</h6>
									<p>{Yii::t('site', 'We focus the simulation on measuring  real managerial skills – their ability to bring value from the first working day. Skill itself is the mastered capacity to carry out pre-determined results with minimum resources.')}</p> 
									<h6>{Yii::t('site', 'Based on best working practices')}</h6>
									<p>{Yii::t('site', 'We developed the assessment based on the selection of crucial practical skills that define manager’s performance with detailed analysis of how these skills become apparent in behaviour')}</p>
									<p>{Yii::t('site', 'We are continuously up-grading managerial skills profile')}</p>
									<h6>{Yii::t('site', 'Uses real work environment, tasks and decision making situations')}</h6> 
									<p>{Yii::t('site', 'In the simulation we have replicated the manager’s everyday life - familiar tasks, situations, interfaces and office environment. It helps manager to be himself and demonstrate his best results.')}</p>
									<h6>{Yii::t('site', 'Based on mathematical methods not just feelings')}</h6>
									<p>{Yii::t('site', 'Each skill is assessed in many instances throughout the simulation forming valid outcome. Just imagine how many days you need in order to do it in real life!')}</p>
									<p>{Yii::t('site', 'We collect and analyse the data of hundreds of participants to manage the simulation.')}</p>
									<p>{Yii::t('site', 'We give you the possibility to compare candidates using clear quantitative criteria.')}</p>
								</td>
							</tr>
						</table>
					</section>
				</article>
				<!--product end-->
				
			</div>
			<!--content end-->
		</div>
		
		<!--footer-->
		<div class="footer">
			<footer>
				<div class="backtotop"><a href="#top">{Yii::t('site', 'Back to top')}</a></div>
				
				<div class="logo"><a href="/">Skiliks</a></div>
				
				<nav>
					<a href="../">{Yii::t('site', 'Home')}</a>
					<a href="#">{Yii::t('site', 'About')}</a>
					<a href="product">{Yii::t('site', 'Product')}</a>
				</nav>
				
				<p class="copyright">Copyright - Skiliks  - 2012</p>
			</footer>
		</div>
		<!--footer end-->
	</body>
</html>