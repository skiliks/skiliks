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
        
            $document = ExcelDocumentTemplate::model()->byName('Сводный бюджет')->find();
            if (!$document) {
                throw new Exception('cant find document');
            }
            
            $result = array();
            $result['result'] = 1;
            $worksheets = ExcelWorksheetTemplate::model()->byDocument($document->id)->findAll();
            foreach($worksheets as $worksheet) {
                $result['worksheets'][] = array(
                    'id' => $worksheet->id,
                    'title' => $worksheet->name
                );
            }
            
            $worksheetId = $result['worksheets'][0]['id'];
            $result['currentWorksheet'] = $worksheetId;
            
            $cells = ExcelWorksheetTemplateCells::model()->byWorksheet($worksheetId)->findAll();
            $columns = array();
            $strings = 0;
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
                $strings++;
            }
            
            $result['strings'] = $strings;
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
    
    
}

?>
