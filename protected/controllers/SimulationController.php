<?php



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
        $sid = (int)Yii::app()->request->getParam('sid', false);
        
        $uid = SessionHelper::getUidBySid($sid);
        if (!$uid) {
            $result = array('result' => 0, 'message' => 'cant find user');
            return $this->_sendResponse(200, CJSON::encode($result));
        }
        
        $model = Simulations::model()->findByAttributes(array('user_id'=>$uid));
        if ($model) {
            $model->delete();
        }
        
        $model = new Simulations();
        $model->user_id = $uid;
        $model->status = 1;
        $model->start = time();
        $model->difficulty = 1;
        $model->insert();
        
        // Сделать вставки в events triggers
        
        $result = array('result' => 1);
        $this->_sendResponse(200, CJSON::encode($result));
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
        
        $result = array('result' => 1);
        $this->_sendResponse(200, CJSON::encode($result));
    }
}

?>
