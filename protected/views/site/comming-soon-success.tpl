<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <link href="favicon.ico" rel="shortcut icon" type="image/x-icon" />
    <title>{Yii::t('site', 'Skiliks - game the skills')}</title>

    <!--[if IE]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="css/style.css" />
</head>

<body>
<div class="container main-page" id="top">

    <!--header-->
    <header>
        <h1><a href="/">Skiliks</a></h1>

        <p class="coming-soon">{Yii::t('site', 'Coming soon')}</p>
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

            <p class="success">{Yii::t('site', 'Thank you! See you soon!')}</p>
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
                    <li>{Yii::t('site', 'Based on bestworking practices')}</li>
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

        <div class="logo"><a href="#">Skiliks</a></div>

        <p class="copyright">Copyright - Skiliks - 2012</p>
    </footer>
</div>
<!--footer end-->
</body>
</html>