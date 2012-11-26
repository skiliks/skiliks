<?php

class AdminController extends AjaxController
{

    public function actionLog()
    {
        $action = Yii::app()->request->getParam('action', false);
        if(isset($action['type'])) {
            $method = "get{$action['type']}";
            if(method_exists('LogHelper', $method)) {
                if(isset($action['data'])) {
                    if(isset($action['params']) AND is_array($action['params'])){
                        $result['data'] = LogHelper::$method($action['data'], array('order_col'=>$action['order_col'],
                                                                                    'order_type'=>$action['order_type'],
                                                                                    'where_col'=>$action['where_col'],
                                                                                    'where_type'=>$action['where_type'],
                                                                                    'where_val'=>$action['where_val'],
                                                                                    'offset' => $action['offset'],
                                                                                    'limit' => $action['offset']
                        ));
                    } else {
                        throw new Exception("Не указаны параметры!");
                    }

                }else{
                    throw new Exception("Не указан тип результата!");
                }
            } else {

            }
        } else {
            throw new Exception("Не указан тип действия!");
        }

        $this->sendJSON($result);
    }
}
