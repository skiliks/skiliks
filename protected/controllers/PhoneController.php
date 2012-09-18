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
                'name' => $character->fio
            );
        }
        
        $result = array();
        $result['result'] = 1;
        $result['data'] = $list;
        
        return $this->_sendResponse(200, CJSON::encode($result));
    }
    
    public function actionStart() {
        $sid = (int)Yii::app()->request->getParam('id', false);  // персонаж
        
        // как dialog/get
    }
}

?>
