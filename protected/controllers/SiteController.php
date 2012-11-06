<?php
class SiteController extends CController
{

    /**
     * Default response format
     * either 'json' or 'xml'
     */
    private $format = 'json';

    public function actionIndex()
    {
	$rows = array("1"=>"2");
	$this->sendJSON($rows);
        //echo 'Hello World';
    }

private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
{
    // set the status
//    $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
//    header($status_header);
    // and the content type
//    header('Content-type: ' . $content_type);

header("HTTP/1.0 200 OK");
header('Content-type: application/json; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);


	echo $body;
	Yii::app()->end();
}
}
?>