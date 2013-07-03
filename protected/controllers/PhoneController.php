<?php

class PhoneController extends SimulationBaseController {
    
    /**
     * Получение списка тем
     */
    public function actionGetThemes() 
    {
        $simulation = $this->getSimulationEntity();
        $this->sendJSON(array(
            'result' => self::STATUS_SUCCESS,
            'data'   => PhoneService::getThemes((int)Yii::app()->request->getParam('id', 0), $simulation)
        ));
    }

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

        $this->sendJSON(array(
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

        $this->sendJSON(array(
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

        $this->sendJSON([
            'result' => 1,
            'data'   => $phone->callBack($simulation, Yii::app()->request->getParam('dialog_code', false))
        ]);
    }
}


