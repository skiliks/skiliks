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
    public function actionGetContacts() {
        $characters = Characters::model()->findAll();
        
        $list = array();
        foreach($characters as $character) {
            $list[] = array(
                'id' => $character->id,
                'name' => $character->fio,
                'title' => $character->title,
                'phone' => $character->phone
            );
        }
        
        $result = array();
        $result['result'] = 1;
        $result['data'] = $list;
        
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionCall() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);  // персонаж
            if (!$sid) throw new Exception("empty sid");
            $id = (int)Yii::app()->request->getParam('id', false);  // персонаж


            $simId = SessionHelper::getSimIdBySid($sid);

            $model = new PhoneCallsModel();
            $model->sim_id = $simId;
            $model->call_date = time();
            $model->call_type = 1;
            $model->from_id = 1;
            $model->to_id = $id; // какому персонажу мы звоним
            $model->insert();

            $result = array();
            $result['result'] = 1;
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
        }
    }

    
    public function actionGetList() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);  // персонаж
            if (!$sid) throw new Exception("empty sid");
            $simId = SessionHelper::getSimIdBySid($sid);
            
            $charactersList = Characters::model()->findAll();
            $characters = array();
            foreach($charactersList as $character) {
                $characters[$character->id] = $character->fio;
            }
            
            $items = PhoneCallsModel::model()->bySimulation($simId)->findAll();
            $list = array();
            foreach($items as $item) {
                // входящие
                if ($item->call_type == 0) {
                    $characterId = $item->from_id;
                }
                else {
                    // исходящие
                    $characterId = $item->to_id;
                }
                
                $list[] = array(
                    'name' => $characters[$characterId],
                    'date' => date('d.m.Y | G:i', $item->call_date),
                    'type' => $item->call_type  // 2 = miss
                );
            }
            
            
            $result = array();
            $result['result'] = 1;
            $result['data'] = $list;
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            $result = array();
            $result['result'] = 0;
            $result['message'] = $exc->getMessage();
            return $this->_sendResponse(200, CJSON::encode($result));
        }
    }
}

?>
