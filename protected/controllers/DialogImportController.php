<?php



/**
 * Description of DialogImportController
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class DialogImportController extends AjaxController{
    
    public function actionImport() {
        
        // http://backend.skiliks.loc/index.php?r=dialogImport/import
        try {
            $service = new DialogImportService();
            $data = $service->import('media/xls/scenario.csv');  // 'media/import.csv'
            var_dump($data);
            die();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }

        
        
        $result = array(
            'data' => $data
        );
        $this->_sendResponse(200, var_export($data, true), 'text/html');
    }
    
    public function actionImportEvents() {
        try {
            $service = new DialogImportService();
            $service->importEvents('media/xls/scenario.csv');  

        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }
    
    public function actionImportReplica() {
        try {
            $service = new DialogImportService();
            $service->importReplica('media/xls/scenario.csv');  

        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }
}

?>
