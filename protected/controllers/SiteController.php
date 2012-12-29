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

        $cs = Yii::app()->clientScript;
        $assetsUrl = $this->getAssetsUrl();;
        $cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.7.2.min.js');
        $cs->registerCssFile($assetsUrl . "/css/style.css");
        $this->render('index', ['assetsUrl' => $assetsUrl]);
    }

    public function actionSite()
    {
        $cs = Yii::app()->clientScript;

        $assetsUrl = $this->getAssetsUrl();
        $config = CJSON::encode(['assetsUrl' => $assetsUrl]);
        $cs->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.7.2.min.js');
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery-ui-1.8.21.custom.min.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.hotkeys.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.balloon.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.cookies.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.mCustomScrollbar.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery/jquery.mousewheel.min.js");
        $cs->registerScriptFile($assetsUrl . "/js/jquery.textchange.min.js");
        $cs->registerScriptFile($assetsUrl . "/js/bootstrap/js/bootstrap.js");
        $cs->registerScriptFile($assetsUrl . "/js/bootstrap/js/bootstrap-alert.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/lib/php.js");
        $cs->registerScriptFile($assetsUrl . "/js/underscore.js");
        $cs->registerCssFile($assetsUrl . "/js/jquery/jquery-ui.css");
        $cs->registerCssFile($assetsUrl . "/js/bootstrap/css/bootstrap.css");
        $cs->registerCssFile($assetsUrl . "/js/jquery/jquery-ui-1.8.23.slider.css");
        $cs->registerCssFile($assetsUrl . "/js/jquery/jquery.mCustomScrollbar.css");
        $cs->registerCssFile($assetsUrl . "/css/main.css");
        $cs->registerScriptFile($assetsUrl . "/js/backbone.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/skwindow.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/skwindowset.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/skmailwindow.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/skdocumentswindow.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/skdialogwindow.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKServer.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/models/SKApplication.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/SKDialogView.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/world/SKLoginView.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/mail/SKMailLetterBaseView.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/mail/SKMailLetterFixedTextView.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/mail/SKMailLetterPhraseListView.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/world/SKSimulationStartView.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/views/world/SKSettingsView.js");
        //конфиги
        $cs->registerScriptFile($assetsUrl . "/js/game/config/config.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/config/imgConfig.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/config/imgConfigPlayersLogos.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/config/imgConfigPlayersLogosAdd.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/config/imgConfigPlayers.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/config/imgConfigCharacters.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/config/imgConfigAnimations.js");

        //плагины
        $cs->registerScriptFile($assetsUrl . "/js/game/adminka/jgridController.js");

        //системные классы
        $cs->registerScriptFile($assetsUrl . "/js/game/input.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/frame_switcher.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/lib/messages.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/mouse.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/imageManager.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/objects.js");

        //lib
        $cs->registerScriptFile($assetsUrl . "/js/game/lib/mathematics.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/lib/sounds.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/lib/videos.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/lib/accounting.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/lib/loading.js");

        //приемник, отправитель
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/sender.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/receiver.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/session.js");

        //движок самой игры
        $cs->registerScriptFile($assetsUrl . "/js/game/dante.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/game_logic.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/drawGame.js");


        //загрузка игрового мира
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/world.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/simulation.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/events.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/dialogController.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/timer.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/icons.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/add_trigger.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/add_assessment.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/add_animation.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/add_documents.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/day_plan.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/excel.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/mail.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/lib/keyboard.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/documents.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/viewer.js");
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/phone.js");

        //регистрация
        $cs->registerScriptFile($assetsUrl . "/js/game/skiliks/register.js");

        //стартер, обязательно последний инклуд // фикс под ИЕ
        $cs->registerScriptFile($assetsUrl . "/js/game/starter.js");
        $this->render('site', ['config' => $config]);
    }

    public function getAssetsUrl()
    {
        return Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.assets'),false, -1, true);
    }


    /**
     * We handle Yii rroes and savethem to Yii.log.
     * User see just standard notise
     */
    public function actionError()
    {
        $this->returnErrorMessage(Yii::app()->errorHandler->error);
    }
}


