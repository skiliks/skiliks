<?php

class PhoneController extends SimulationBaseController {
    
    /**
     * Получение списка тем
     */
    public function actionGetThemes() 
    {
        $simulation = $this->getSimulationEntity();
        $this->sendJSON(array(
            'result' => 1,
            'data'   => PhoneService::getThemes((int)Yii::app()->request->getParam('id', 0), $simulation)
        ));
    }
    
    /**
     * Похоже никогда не вызывается
     *
     * @return HttpResponce
     * 
     * @throws Exception
     */
    /*public function actionIgnore()
    {
        try {     
                
            PhoneService::registerMissed(
                    $this->getSimulationId(), 
                    (int)Yii::app()->request->getParam('dialogId', null),
                    Yii::app()->request->getParam('time', 0)*60
            );
            
            $dialog = new DialogService();
            $dialog->getDialog($this->getSimulationId(), 
                    (int)Yii::app()->request->getParam('dialogId', null), 
                    Yii::app()->request->getParam('time', 0) *60 );
            
            return $this->sendJSON(array(
                'result' => 1
            ));
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
        
    }*/
    
    /**
     * Вызов телефона
     * @return type 
     */
    public function actionCall() 
    {
        $this->sendJSON(
            PhoneService::call(
                $this->getSimulationEntity(),
                (int)Yii::app()->request->getParam('themeId', false),
                (int)Yii::app()->request->getParam('contactId', false),
                Yii::app()->request->getParam('time', '00:00')
        ));
    }

    /**
     *
     */
    public function actionGetList() 
    {
        $simulation = $this->getSimulationEntity();

        return $this->sendJSON(array(
            'result' => 1,
            'data'   => PhoneService::getMissedCalls($simulation)
        ));
    }
    /**
     *
     */
    public function actionMarkMissedCallsDisplayed()
    {
        $simulation = $this->getSimulationEntity();

        return $this->sendJSON(array(
            'result' => 1,
            'data'   => PhoneService::markMissedCallsDisplayed($simulation)
        ));
    }

    /**
     *
     */
    public function actionCallBack()
    {
        $phone = new PhoneService();
        $simulation = $this->getSimulationEntity();

        return $this->sendJSON([
            'result' => 1,
            'data'   => $phone->callBack($simulation, Yii::app()->request->getParam('dialog_code', false))
        ]);
    }
}


