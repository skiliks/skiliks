<?php

include_once('protected/controllers/AjaxController.php');

/**
 * Description of CharactersPointsTitles
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersPointsTitlesController extends AjaxController{
    
    public function actionDraw()
    {
        $rows = array(
            'result' => 1,
            'rows' => 'rows'
        );
        
	$this->_sendResponse(200, CJSON::encode($rows));
    }
}

?>
