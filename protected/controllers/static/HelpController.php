<?php
/**
 * Created by JetBrains PhpStorm.
 * User: macbookpro
 * Date: 01.10.13
 * Time: 09:13
 * To change this template use File | Settings | File Templates.
 */
class HelpController extends SiteBaseController
{

    function actionGeneral() {
        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/help-1280.css');
        $this->addSiteCss('pages/help-1024.css');

        $this->addSiteJs('_page-help.js');
        $this->addSiteJs('_start_demo.js');

        $this->render("help_general");
    }

    function actionCorporate() {
        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/help-1280.css');
        $this->addSiteCss('pages/help-1024.css');

        $this->addSiteJs('_page-help.js');
        $this->addSiteJs('_start_demo.js');

        $this->render("help_corporate");
    }

    function actionPersonal() {
        $this->layout = 'site_standard_2';

        $this->addSiteCss('pages/help-1280.css');
        $this->addSiteCss('pages/help-1024.css');

        $this->addSiteJs('_page-help.js');
        $this->addSiteJs('_start_demo.js');

        $this->render("help_personal");
    }


}