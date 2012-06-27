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
        
        $model = Simulations::model()->findByAttributes(array('user_id'=>$uid));
        if ($model) {
            $model->delete();
        }
        
        $model = new Simulations();
        $model->user_id = $uid;
        $model->status = 1;
        $model->start = time();
        $model->diffilculty = 1;
        $model->insert();
    }
    
    /**
     * Остановка симуляции
     */
    public function actionStop() {
        $uid = (int)Yii::app()->request->getParam('uid', false);
        
        $model = Simulations::model()->findByAttributes(array('user_id'=>$uid));
        if ($model) {
            $model->end = time();
            $model->status = 0;
            $model->save();
        }
    }
}

?>
