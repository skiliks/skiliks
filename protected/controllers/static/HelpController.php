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
        $this->render("help_general");
    }

    function actionCorporate() {
        $this->render("help_corporate");
    }

    function actionPersonal() {
        $this->render("help_personal");
    }


}