<?php

header("HTTP/1.0 200 OK");
header('Content-type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Access-Control-Allow-Origin: *");

set_time_limit(0);

require_once '../lib/excel/PHPExcel/IOFactory.php';

class ExcelImporter {
    
    protected $_db;

    protected $_phpExcel;
    
    protected $_rowStmt = false;

    function __construct() {
        $this->_db = new PDO('mysql:host=localhost;dbname=skiliks', 'root', '');
        $sql = "set character_set_client='utf8'";
        $this->_db->query($sql);
        $sql = "set character_set_results='utf8'";
        $this->_db->query($sql);
        $sql = "set collation_connection='utf8_general_ci'";
        $this->_db->query($sql);
    }
    
    protected function _createDocument($documentName) {
        $sql = "insert into excel_document_template (name) values (:name)";
        $stm = $this->_db->prepare($sql);
        $stm->bindParam(':name', $documentName, PDO::PARAM_STR);
        $stm->execute();
        return (int)$this->_db->lastInsertId();
    }
    
    protected function _createWorksheet($documentId, $worksheetName) {
        $sql = "insert into excel_worksheet_template (document_id, name) values (:documentId, :name)";
        $stm = $this->_db->prepare($sql);
        $stm->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stm->bindParam(':name', $worksheetName, PDO::PARAM_STR);
        $stm->execute();
        return (int)$this->_db->lastInsertId();
    }
    
    protected function _insertRow($params) {
        if (!$this->_rowStmt) {
            $sql = "insert into excel_worksheet_template_cells 
                    (worksheet_id, `string`, `column`, `value`, `read_only`, `formula`) 
                    values (:worksheetId, :string, :column, :value, :readOnly, :formula)";
            $this->_rowStmt = $this->_db->prepare($sql);
        }
        
        $this->_rowStmt->bindParam(':worksheetId', $params['worksheetId'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':string', $params['string'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':column', $params['column'], PDO::PARAM_STR);
        $this->_rowStmt->bindParam(':value', $params['value'], PDO::PARAM_STR);
        $this->_rowStmt->bindParam(':readOnly', $params['readOnly'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':formula', $params['formula'], PDO::PARAM_STR);
        if (!$this->_rowStmt->execute()) {
            
            throw new Exception(var_export($this->_rowStmt->errorInfo(), true));
        }
        
        
        return (int)$this->_db->lastInsertId();
    }
    
    public function import($documentName) {
        $this->_phpExcel = PHPExcel_IOFactory::load("../media/test.xlsx");
        
        $documentId = $this->_createDocument($documentName);

        
        foreach ($this->_phpExcel->getWorksheetIterator() as $worksheet) {
            $worksheetTitle     = $worksheet->getTitle();
            
            $worksheetId = $this->_createWorksheet($documentId, $worksheetTitle);
            
            $highestRow         = $worksheet->getHighestRow(); // например, 10
            $highestColumn      = $worksheet->getHighestColumn(); // например, 'F'
            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
            $nrColumns = ord($highestColumn) - 64;
            echo "<br>В таблице ".$worksheetTitle." ";
            echo $nrColumns . ' колонок (A-' . $highestColumn . ') ';
            echo ' и ' . $highestRow . ' строк.';
            echo '<br>Данные: <table border="1"><tr>';
            for ($row = 1; $row <= $highestRow; ++ $row)
            {
                echo '<tr>';
                for ($col = 0; $col < $highestColumnIndex; ++ $col)  {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $columnName = $cell->stringFromColumnIndex($col);
                    $val = $cell->getValue();
                    $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);
                    
                    if (is_null($val)) $val = '';
                    $formula = '';
                    if ($dataType == 'f') {
                        $formula = $val;   
                        $val = '';
                    }
                    
                    $params = array(
                        'worksheetId' => $worksheetId,
                        'string' => $col,
                        'column' => $columnName,
                        'value' => $val,
                        'readOnly' => 1,
                        'formula' => $formula
                    );
                    $rowId = $this->_insertRow($params);
                    if ($rowId == 0) throw new Exception('cant insert : '.var_export($params, true));

                    echo '<td>' . $val . '<br>(Тип ' . $dataType . ')'.$columnName.'</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
        }
    }
}

try {
    $import = new ExcelImporter();
    $import->import('Сводный бюджет');
} catch (Exception $exc) {
    echo 'Exception : '.$exc->getMessage();
}


echo(time());




?>