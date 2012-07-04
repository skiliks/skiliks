<?php



/**
 * Description of DialogImportController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogImportController extends AjaxController{
    
    public function actionImport() {
        // http://backend.skiliks.loc/index.php?r=dialogImport/import
        $service = new DialogImportService();
        $data = $service->import();
        
        $result = array(
            'data' => $data
        );
        //$this->_sendResponse(200, var_export($data, true), 'text/html');
    }
}

?>
