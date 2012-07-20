<?php



/**
 * Контроллера документа Excel
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class ExcelDocumentController extends AjaxController{
    
    public function actionGet() {
        try {
            $sid = Yii::app()->request->getParam('sid', false);  
            if (!$sid) throw new Exception('wrong sid');
            $simId = SessionHelper::getSimIdBySid($sid);
            if (!$simId) throw new Exception("cant find simId by sid {$sid}");
        
            //$document = ExcelDocumentTemplate::model()->byName('Сводный бюджет')->find();
            $document = ExcelDocument::model()->bySimulation($simId)->find();
            if (!$document) {
                throw new Exception('cant find document');
            }
            
            $result = array();
            $result['result'] = 1;
            //$worksheets = ExcelWorksheetTemplate::model()->byDocument($document->id)->findAll();
            $worksheets = ExcelWorksheet::model()->byDocument($document->id)->findAll();
            foreach($worksheets as $worksheet) {
                $result['worksheets'][] = array(
                    'id' => $worksheet->id,
                    'title' => $worksheet->name
                );
            }
            
            $worksheetId = $result['worksheets'][0]['id'];
            $result['currentWorksheet'] = $worksheetId;
            
            //$cells = ExcelWorksheetTemplateCells::model()->byWorksheet($worksheetId)->findAll();
            $cells = ExcelWorksheetCells::model()->byWorksheet($worksheetId)->findAll();
            $columns = array();
            $strings = array();
            foreach($cells as $cell) {
                $result['worksheetData'][] = array(
                    'id' => $cell->id,
                    'string' => $cell->string,
                    'column' => $cell->column,
                    'value' => $cell->value,
                    'read_only' => $cell->read_only,
                    'comment' => $cell->comment,
                    'formula' => $cell->formula,
                    'colspan' => $cell->colspan,
                    'rowspan' => $cell->rowspan
                );
                
                $columns[$cell->column] = 1;
                $strings[$cell->string] = 1;
            }
            
            $result['strings'] = count($strings);
            $result['columns'] = count($columns);
            
            return $this->_sendResponse(200, CJSON::encode($result));
        } catch (Exception $exc) {
            return $this->_sendResponse(200, CJSON::encode(array(
                'result' => 0,
                'message' => $exc->getMessage(),
                'code' => $exc->getCode()
            )));
        }
    }
    
    public function actionCopy() {
        $sid = Yii::app()->request->getParam('sid', false);  
        if (!$sid) throw new Exception("Не передан sid");
        $simId = SessionHelper::getSimIdBySid($sid);
            
        ExcelDocumentService::copy('Сводный бюджет', $simId);
    }
    
}

?>
