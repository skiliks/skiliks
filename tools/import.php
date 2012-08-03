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

    protected $_columns = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R');
    
    function __construct() {
        $this->_db = new PDO('mysql:host=localhost;dbname=skiliks', 'root', '');
        $sql = "set character_set_client='utf8'";
        $this->_db->query($sql);
        $sql = "set character_set_results='utf8'";
        $this->_db->query($sql);
        $sql = "set collation_connection='utf8_general_ci'";
        $this->_db->query($sql);
        
        $this->_clearTables();
    }
    
    protected function _clearTables() {
        $tables = array('excel_document_template', 'excel_worksheet_template', 'excel_worksheet_template_cells');
        
        foreach($tables as $tableName) {
            $sql = "delete from {$tableName}";
            $stm = $this->_db->prepare($sql);
            $stm->execute();
        }
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
        // проверим, а не существует ли такая ячейка
        $sql = "select *
                from excel_worksheet_template_cells 
                where 
                    worksheet_id = :worksheetId
                    and string = :string
                    and column = :column";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':worksheetId', $params['worksheetId'], PDO::PARAM_INT);
        $stmt->bindParam(':string', $params['string'], PDO::PARAM_INT);
        $stmt->bindParam(':column', $params['column'], PDO::PARAM_STR);
        $stmt->execute();
        $f = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($f['id'])) {
            var_dump($f); die();
        }
        
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
        $this->_phpExcel = PHPExcel_IOFactory::load("../media/test3.xlsx");
        
        $documentId = $this->_createDocument($documentName);
        if (!$documentId) throw new Exception("cant get documentId for $documentName");

        
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
            for ($row = 1; $row <= $highestRow; ++ $row)    {
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
                        //echo"formula = $formula";
                        $formula = str_replace(',', ';', $formula);
                    }
                    
                    $style = $this->_phpExcel->getActiveSheet()->getStyle($columnName.$row);
                    //var_dump($style); die();
                    
                    // getFont
                    
                    // так мы можем вытащить цвет
                    $color = $this->_phpExcel->getActiveSheet()->getStyle($columnName.$row)->getFill()->getStartColor()->getARGB();
                    echo('color='.$color);
                    //$this->_phpExcel->getSheetByName('Sheet1')->getStyle("B13")->getFont()->getBold()
                    $bold = $this->_phpExcel->getActiveSheet()->getStyle($columnName.$row)->getFont()->getBold();
                    echo('bold='.$bold);
                    
             
                    $params = array(
                        'worksheetId' => $worksheetId,
                        'string' => $row,
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
            
            
            // Добивка недостающих колонок
            if ($highestColumnIndex < count($this->_columns)) {
                $columnTo = count($this->_columns);
                for ($i = $highestColumnIndex; $i < $columnTo; $i++) {
                    for ($row = 1; $row <= $highestRow; ++ $row)    {
                        $params = array(
                            'worksheetId' => $worksheetId,
                            'string' => $row,
                            'column' => $this->_columns[$i],
                            'value' => '',
                            'readOnly' => 0,
                            'formula' => ''
                        );
                        $rowId = $this->_insertRow($params);
                        if ($rowId == 0) throw new Exception('cant insert : '.var_export($params, true));
                    }
                }
            }
            // теперь добьем недостающие строки - если это надо
            if ($highestRow <26) {
                $columnTo = count($this->_columns);
                for ($row = $highestRow+1; $row <= 26; ++ $row)    {
                    for ($i = 0; $i < $columnTo; $i++) {
                        $params = array(
                            'worksheetId' => $worksheetId,
                            'string' => $row,
                            'column' => $this->_columns[$i],
                            'value' => '',
                            'readOnly' => 0,
                            'formula' => ''
                        );
                        $rowId = $this->_insertRow($params);
                        if ($rowId == 0) throw new Exception('cant insert : '.var_export($params, true));
                    }
                }
                
            }
        }
        
        
        
    }  // of import
}

try {
    $import = new ExcelImporter();
    $import->import('Сводный бюджет');
} catch (Exception $exc) {
    echo 'Exception : '.$exc->getMessage();
}


echo(time());




?>