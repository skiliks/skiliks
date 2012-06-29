<?php



/**
 * Движек событий
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsController extends AjaxController{
    
    
    /**
     * Опрос состояния событий
     */
    public function actionGetState() {
        $simId = (int)Yii::app()->request->getParam('simId', false);  //sid
    }
}

?>
