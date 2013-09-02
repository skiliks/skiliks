<?php

class DialogController extends SimulationBaseController {
    
    /**
     * Загрузка заданного диалога
     * @return type 
     */
    public function actionGet() 
    {
        $dialog = new DialogService();
        $json = $dialog->getDialog(
                $this->getSimulationEntity()->id,
                (int)Yii::app()->request->getParam('dialogId', 0),
                Yii::app()->request->getParam('time', false));

        $this->sendJSON($json);
    }
}


