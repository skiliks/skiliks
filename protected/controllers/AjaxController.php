<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxController
 *
 * @author dorian
 */
class AjaxController extends CController{
    
    protected function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
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
header("Access-Control-Allow-Origin: *");


	echo $body;
	Yii::app()->end();
}
}

?>
