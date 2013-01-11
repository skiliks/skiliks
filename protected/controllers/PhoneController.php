<?php

/**
 * Контроллер телефона
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class PhoneController extends AjaxController{
    

    /**
     * Получение списка контактов
     */
    public function actionGetContacts() 
    {
        return $this->sendJSON(array(
            'result' => 1,
            'data'   => Characters::model()->getContactsList()
        ));
    }
    
    /**
     * Получение списка тем
     */
    public function actionGetThemes() 
    {
        return $this->sendJSON(array(
            'result' => 1,
            'data'   => PhoneService::getThemes((int)Yii::app()->request->getParam('id', 0))
        ));
    }
    
    /**
     * @return HttpResponce
     * 
     * @throws Exception
     */
    public function actionIgnore() 
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
        
    }
    
    /**
     * Вызов телефона
     * @return type 
     */
    public function actionCall() 
    {
        $simulation = $this->getSimulationEntity();
        
        try {
            $themeId = (int)Yii::app()->request->getParam('themeId', false);  // идентификатор темы
            $characterId = (int)Yii::app()->request->getParam('id', false);  // идентификатор темы
            $this->sendJSON(
                PhoneService::call($simulation, $themeId, $characterId)
            );
        } catch (Exception $e) {
            $this->returnErrorMessage($e->getMessage());
        }
    }

    
    public function actionGetList() 
    {
        $simulation = $this->getSimulationEntity();

        return $this->sendJSON(array(
            'result' => 1,
            'data'   => PhoneService::getMissedCalls($simulation)
        ));
    }
}


