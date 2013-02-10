<?php
class SiteController extends AjaxController
{
    /**
     * This is defaut Yii action.
     * It never useded in API or frontend static pages.
     * So, we display error message for user if aout sscript call this action.
     */
    public function actionIndex()
    {
        $assetsUrl = $this->getAssetsUrl();

        $this->render('index', ['assetsUrl' => $assetsUrl]);
    }

    public function actionComingSoonSuccess()
    {
        $cs = Yii::app()->clientScript;
        $assetsUrl = $this->getAssetsUrl();

        $cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.7.2.min.js');
        $cs->registerCssFile($assetsUrl . "/css/style.css");
        $this->render('comming-soon-success', ['assetsUrl' => $assetsUrl]);
    }

    public function actionSite()
    {
        $cs = Yii::app()->clientScript;

        $assetsUrl = $this->getAssetsUrl();
        $config = Yii::app()->params['public'];
        $config['assetsUrl'] = $assetsUrl;

        $cs->registerCssFile($assetsUrl . "/js/jquery/jquery-ui.css");
        $cs->registerCssFile($assetsUrl . "/js/bootstrap/css/bootstrap.css");
        $cs->registerCssFile($assetsUrl . "/js/jquery/jquery-ui-1.8.23.slider.css");
        $cs->registerCssFile($assetsUrl . "/js/jquery/jquery.mCustomScrollbar.css");
        $cs->registerCssFile($assetsUrl . "/js/elfinder-2.0-rc1/css/elfinder.min.css");
        $cs->registerCssFile($assetsUrl . "/js/elfinder-2.0-rc1/css/theme.css");
        $cs->registerCssFile($assetsUrl . "/css/tag-handler.css");
        $cs->registerCssFile($assetsUrl . "/css/main.css");
        $cs->registerLinkTag('stylesheet/less', 'text/css', $assetsUrl . "/css/simulation.less");
        $cs->registerLinkTag('stylesheet/less', 'text/css', $assetsUrl . "/css/plan.less");
        $cs->registerLinkTag('stylesheet/less', 'text/css', $assetsUrl . "/css/documents.less");

        $cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.7.2.min.js');
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery-ui-1.8.24.custom.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.hotkeys.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.balloon.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.topzindex.min.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.cookies.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery-skiliks.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.mCustomScrollbar.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.mousewheel.min.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.tablesorter.js");

        $cs->registerScriptFile($assetsUrl . "/js/bootstrap/js/bootstrap.js");
        $cs->registerScriptFile($assetsUrl . "/js/bootstrap/js/bootstrap-alert.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/lib/pdf.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/lib/hyphenate.js");
        $cs->registerScriptFile($assetsUrl . "/js/underscore.js");
        $cs->registerScriptFile($assetsUrl . "/js/prefixfree.min.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery.ddslick.min.js");
        $cs->registerScriptFile($assetsUrl . '/js/require.js');

        $cs->registerScriptFile($assetsUrl . "/js/backbone.js");
        $cs->registerScriptFile($assetsUrl . "/js/less-1.3.3.min.js");
        $cs->registerScriptFile($assetsUrl . "/js/elfinder-2.0-rc1/js/elfinder.min.js");

        $cs->registerScriptFile($assetsUrl . "/js/tag-handler/jquery.taghandler.min.js");

        $cs->registerScriptFile($assetsUrl . "/js/game/models/window/SKMailWindow.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/window/SKDocumentsWindow.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKEvent.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKTodoTask.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKDayTask.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKPhoneContact.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKPhoneTheme.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKDocument.js");

        $cs->registerScriptFile($assetsUrl . "/js/game/collections/SKPhoneContactsCollection.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/collections/SKPhoneThemeCollection.js");

        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKMailTask.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKMailPhrase.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKCharacter.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKMailSubject.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKMailFolder.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKEmail.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKAttachment.js");


        $cs->registerScriptFile($assetsUrl . "/js/game/views/dialogs/SKImmediateVisitView.js");

        $cs->registerScriptFile($assetsUrl . "/js/game/views/world/SKLoginView.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/world/SKDebugView.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/world/SKIconPanelView.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/world/SKSettingsView.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/develop_mode/SKFlagStateView.js");

        $cs->registerScriptFile($assetsUrl . "/js/raven-0.7.1.js");

        //плагины
        $cs->registerScriptFile($assetsUrl . "/js/game/adminka/jgridController.js");
        $this->layout = false;
        $this->render('site', ['config' => CJSON::encode($config), 'assetsUrl' => $assetsUrl]);
    }

    /**
     * We handle Yii rroes and savethem to Yii.log.
     * User see just standard notice
     */
    public function actionError()
    {
        $this->returnErrorMessage(Yii::app()->errorHandler->error);
    }
}


