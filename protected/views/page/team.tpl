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
                    <a href="product">{Yii::t('site', 'Product')}</a>
                </nav>
            </header>
            <!--header end-->

            <!--content-->
            <div class="content">

                <!--team-->
                <article class="team">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut at nulla risus. Curabitur sapien diam, faucibus nec pretium sit amet, faucibus quis turpis. Fusce at consequat dolor. Maecenas sit amet augue et leo iaculis posuere eu quis odio. Phasellus hendrerit, nunc pulvinar tempor tempus, dolor arcu congue nulla, id venenatis dui urna nec lorem. Pellentesque condimentum tempor scelerisque. Maecenas eget quam nisi, venenatis rhoncus sem. Etiam scelerisque rutrum erat a porttitor. Integer sit amet metus at nisl vestibulum suscipit non et lorem.</p>
                    <p>Vivamus vitae accumsan velit. Nullam mi lorem, tincidunt vitae pretium sed, scelerisque et magna. In sagittis felis sit amet sapien sagittis consequat. Nullam eros odio, molestie ac feugiat quis, fermentum elementum mi. Mauris scelerisque cursus imperdiet. Nullam dictum rutrum velit, et fringilla arcu rhoncus non. Cras quis urna lectus.</p>
                    <p>Ut quis turpis a eros condimentum euismod. Vivamus placerat fermentum lorem, eu cursus quam vestibulum nec. Phasellus scelerisque rhoncus dolor sagittis pretium. Sed lacus quam, porttitor porta laoreet at, fermentum ut magna. Quisque ut hendrerit ligula. Suspendisse potenti. In hac habitasse platea dictumst. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque est nisi, euismod vitae luctus sit amet, convallis nec nunc.</p>
                    <p>Vivamus fringilla, eros sed semper blandit, diam massa condimentum nunc, vitae congue felis metus scelerisque tortor. Pellentesque ornare gravida arcu ut facilisis. In a neque mi, sit amet pretium urna. Nulla ullamcorper tortor quis mauris ullamcorper posuere. Integer non convallis enim. Vestibulum purus nunc, consequat eget semper vel, ultricies nec magna. Nulla ligula magna, condimentum in volutpat ut, volutpat sit amet elit. Vivamus vitae egestas nibh.</p>
                </article>
                <!--team end-->

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