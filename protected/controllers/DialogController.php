<?php
/**
 * Контроллер диалога для клиентской части
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogController extends AjaxController{
    
    /**
     * Загрузка заданного диалога
     * @return type 
     */
    public function actionGet() 
    {

        $dialog = new DialogService();
        $json = $dialog->getDialog(
                $this->getSimulationId(), 
                (int)Yii::app()->request->getParam('dialogId', 0), 
                Yii::app()->request->getParam('time', false));
        
        $this->sendJSON($json);
    }
}


