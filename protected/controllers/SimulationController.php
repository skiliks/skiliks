<?php

include_once('protected/controllers/AjaxController.php');

/**
 * Контроллер симуляции
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class SimulationController extends AjaxController{
    
    /**
     * Старт симуляции
     */
    public function actionStart() {
        $uid = (int)Yii::app()->request->getParam('uid', false);
    }
    
    /**
     * Остановка симуляции
     */
    public function actionStop() {
        $uid = (int)Yii::app()->request->getParam('uid', false);
    }
}

?>
