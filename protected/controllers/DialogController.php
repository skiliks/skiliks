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
    public function actionGet() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);  
            if (!$sid) throw new Exception('Не задан sid', 1);
            
            $dialogId = (int)Yii::app()->request->getParam('dialogId', false);  
            if (!$dialogId) throw new Exception('Не задан диалог', 2);
            
            $dialog = DialogService::get($dialogId);
            if (!$dialog) throw new Exception('Не могу загрузить диалог', 3);
            
            
            // @todo: загрузить диалог по nextBranch
            $dialog = DialogService::get($dialog->next_branch);
            if (!$dialog) throw new Exception('Не могу загрузить диалог по ветке', 4);
            
            // @todo: загрузить варианта ответов
            $data = array();
            $data[] = DialogService::dialogToArray($dialog);

            // загрузить те, где branch = next_branch
            $dialogs = Dialogs::model()->byBranch($dialog->next_branch)->findAll();
            if (!$dialogs) throw new Exception('Не могу загрузить варианты ответов', 5);
            foreach($dialogs as $dialog) {
                $data[] = DialogService::dialogToArray($dialog);
            }

            return $this->_sendResponse(200, CJSON::encode(array('result' => 1, 'data' => $data)));
            
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode()
            )));
        }
    }
}

?>
