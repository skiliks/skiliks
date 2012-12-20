<?php
/**
 * Description of ViewerController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ViewerController extends AjaxController
{    
    /**
     * Получение файла
     */
    public function actionGet() 
    {
        try {            
            return $this->sendJSON(array(
                'result' => 1, 
                'data'   => MyDocumentsService::getDocumentPages(
                    (int)Yii::app()->request->getParam('fileId', 0)
                )
            ));
        } catch (Exception $exc) {
            return $this->sendJSON(array(
                'result'  => 0, 
                'message' => $exc->getMessage()
            ));
        }
    }
}


