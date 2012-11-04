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

    public $is_test = false;
    
    protected function _sendResponse($status = 200, $body = '', $content_type = 'application/json')
    {
        if (!$this->is_test) {
            header("HTTP/1.0 200 OK");
            header('Content-type: '.$content_type.'; charset=UTF-8');
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Access-Control-Allow-Origin: *");
        }

	    echo $body;

        if (!$this->is_test)
	        Yii::app()->end();
    }

    protected function sendJSON($data, $status = 200) {
        $this->_sendResponse($status, CJSON::encode($data));
    }
}

?>
