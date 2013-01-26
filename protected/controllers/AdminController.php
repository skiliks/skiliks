<?php

class AdminController extends AjaxController
{

    public function actionLog()
    {
        $send_json = true;
        $action = array(
            'type' => Yii::app()->request->getParam('type','DialogDetail'),
            'data' => (string)Yii::app()->request->getParam('data','json'),
            'params' => array('order_col')
        );
        $result = array('result'=>1, 'message'=>"Done");
        try {
        if(isset($action['type'])) {
            $method = "get{$action['type']}";
            if(method_exists('LogHelper', $method)) {
                if(isset($action['data'])) {
                    if(isset($action['params']) AND is_array($action['params'])) {

                        $db_data = LogHelper::$method($action['data']);
                        if(is_array($db_data)){
                            $result += $db_data;
                        }else{
                            $send_json = false;
                        }
                        
                    } else {
                        throw new Exception("Не указаны параметры!");
                    }

                }else{
                    throw new Exception("Не указан тип результата!");
                }
            } else {
                throw new Exception("Не найдено действие!");    
            }
        } else {
            throw new Exception("Не указан тип действия!");
        }
        
    }
    catch (Exception $e) {
        $result = array('result'=>0, 'message'=>$e->getMessage(), 'data'=>null);
    }
    if($send_json){
        $this->sendJSON($result);
    }
    }
    
    public function actionIndex() {
        $assetsUrl = $this->getAssetsUrl();
        
        $config = Yii::app()->params['public'];
        $config['assetsUrl'] = $assetsUrl;
        
        Yii::app()->clientScript->registerCssFile($assetsUrl . '/js/jquery/jquery-ui.css')
            ->registerCssFile($assetsUrl . '/js/bootstrap/css/bootstrap.css')
            ->registerCssFile($assetsUrl . '/js/jgrid/css/ui.multiselect.css')
            ->registerCssFile($assetsUrl . '/js/jgrid/css/ui.jqgrid.css')
            ->registerCssFile($assetsUrl . '/js/jgrid/css/jquery-ui-1.8.2.custom.css');
        
        Yii::app()->clientScript->registerScriptFile($assetsUrl . '/js/jquery/jquery-1.7.2.min.js')
            ->registerScriptFile($assetsUrl . "/js/jquery/jquery-ui-1.8.24.custom.js")
            ->registerScriptFile($assetsUrl . "/js/jquery/jquery.hotkeys.js")
            ->registerScriptFile($assetsUrl . "/js/jquery/jquery.balloon.js")
            ->registerScriptFile($assetsUrl . "/js/bootstrap/js/bootstrap.js")
            ->registerScriptFile($assetsUrl . "/js/bootstrap/js/bootstrap-alert.js")
            ->registerScriptFile($assetsUrl . "/js/jgrid/js/jquery.jqGrid.min.js")
            ->registerScriptFile($assetsUrl . "/js/jgrid/js/i18n/grid.locale-ru.js")
            ->registerScriptFile($assetsUrl . "/js/game/lib/php.js");
            // ->registerScriptFile($assetsUrl . "/js/game/adminka/skiliks/engine_loader.js")

        $jsScriptsAtTheEndOfBody = '';
        $scripts = [
            "js/game/adminka/config.js",
            "js/game/adminka/jgridController.js",
            "js/game/adminka/frame_switcher.js",
            "js/game/lib/messages.js",
            "js/game/mouse.js",
            "js/game/game_logic.js",
            "js/game/skiliks/sender.js",
            "js/game/skiliks/receiver.js",
            "js/game/lib/loading.js",
            "js/game/adminka/menu_main.js",
            "js/game/adminka/world.js",
            "js/game/adminka/skiliks/characters_points_titles/characters_points_titles.js",
            "js/game/adminka/skiliks/dialog_branches/dialog_branches.js",
            "js/game/adminka/skiliks/dialogs/dialogs.js",
            "js/game/adminka/skiliks/events_results/events_results.js",
            "js/game/adminka/skiliks/events_samples/events_samples.js",
            "js/game/adminka/skiliks/events_choices/events_choices.js",
            "js/game/adminka/skiliks/scenario/scenario.js",
            "js/game/adminka/skiliks/logging/logging.js",
            "js/game/adminka/starter.js",  
        ];
        foreach ($scripts as $path) {
            $jsScriptsAtTheEndOfBody .= sprintf(
                '<script type="text/javascript" src="%s/%s"></script>', $assetsUrl, $path
            );
        }
        
        $this->render(
            'index', 
            [
                'config'    => CJSON::encode($config), 
                'assetsUrl' => $assetsUrl,
                'jsScripts' => $jsScriptsAtTheEndOfBody,
            ]
       );
    }
}
