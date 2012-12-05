<?php

class AdminController extends AjaxController
{

    public function actionLog()
    {
        //$action = Yii::app()->request->getParam('action', false);
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
                        $result += (array)LogHelper::$method($action['data']);
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

    $this->sendJSON($result);
    }
}
