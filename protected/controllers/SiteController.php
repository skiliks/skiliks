<?php
class SiteController extends CController
{
    public function actionIndex()
    {
        $rows = array("Error" => "Controller/action not founded.");
        $this->_sendResponse(200, CJSON::encode($rows));
    }

    public function actionError()
    {
        $error=Yii::app()->errorHandler->error;
        $result = array();
        $result['result'] = 0;
        $result['message'] = $error;
        $this->_sendResponse(200, CJSON::encode($result), 'application/json');
    }

    protected function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
    {
        header("HTTP/1.0 200 OK");
        header('Content-type: application/json; charset=UTF-8');
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);

        echo $body;
        Yii::app()->end();
    }
}


