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
        
        //$this->_clearTables();
    }
    
    protected function _extractCellName($cellName) {
        if (preg_match_all("/(\w+)\d+/", $cellName, $matches)) {
            return $matches[1][0];
        } 
        return false;
    }
    
    /**
     * Создает файл. Если файл существует возвращает его id.
     * @param string $fileName
     * @return int
     */
    protected function _createFile($fileName) {
        $sql = "select id from my_documents_template where fileName = :fileName";
        $stm = $this->_db->prepare($sql);
        $stm->bindParam(':fileName', $fileName, PDO::PARAM_STR);
        $stm->execute();
        $f = $stm->fetch(PDO::FETCH_ASSOC);
        if (isset($f['id'])) return (int)$f['id'];
        
        // создаем новый файл
        $sql = "insert into my_documents_template (fileName) values (:fileName)";
        $stm = $this->_db->prepare($sql);
        $stm->bindParam(':fileName', $fileName, PDO::PARAM_STR);
        $stm->execute();
        return (int)$this->_db->lastInsertId();
    }
    
    protected function _clearTables() {
        $tables = array('excel_document_template', 'excel_worksheet_template', 'excel_worksheet_template_cells');
        
        foreach($tables as $tableName) {
            $sql = "delete from {$tableName}";
            $stm = $this->_db->prepare($sql);
            $stm->execute();
        }
    }
    
    /**
     *  Создает excel-документ
     *  @param string $documentName имя документа
     *  @param int $fileId идентификатор файла
     */
    protected function _createDocument($documentName, $fileId) {
        $sql = "insert into excel_document_template (name, file_id) values (:name, :fileId)";
        $stm = $this->_db->prepare($sql);
        $stm->bindParam(':name', $documentName, PDO::PARAM_STR);
        $stm->bindParam(':fileId', $fileId, PDO::PARAM_INT);
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
                    (worksheet_id, `string`, `column`, `value`, `read_only`, 
                    `formula`, `bold`, `color`, `font`, `fontSize`, `width`, 
                    `colspan`, borderTop, borderBottom, borderLeft, borderRight) 
                    values (:worksheetId, :string, :column, :value, :readOnly, 
                    :formula, :bold, :color, :font, :fontSize, :width, 
                    :colspan, :borderTop, :borderBottom, :borderLeft, :borderRight)";
            $this->_rowStmt = $this->_db->prepare($sql);
        }
        
        //echo($sql);
        //var_dump($params);
        
        $this->_rowStmt->bindParam(':worksheetId', $params['worksheetId'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':string', $params['string'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':column', $params['column'], PDO::PARAM_STR);
        $this->_rowStmt->bindParam(':value', $params['value'], PDO::PARAM_STR);
        $this->_rowStmt->bindParam(':readOnly', $params['readOnly'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':formula', $params['formula'], PDO::PARAM_STR);
        $this->_rowStmt->bindParam(':bold', $params['bold'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':color', $params['color'], PDO::PARAM_STR);
        $this->_rowStmt->bindParam(':font', $params['font'], PDO::PARAM_STR);
        $this->_rowStmt->bindParam(':fontSize', $params['fontSize'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':width', $params['width'], PDO::PARAM_STR);
        $this->_rowStmt->bindParam(':colspan', $params['colspan'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':borderTop', $params['borderTop'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':borderBottom', $params['borderBottom'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':borderLeft', $params['borderLeft'], PDO::PARAM_INT);
        $this->_rowStmt->bindParam(':borderRight', $params['borderRight'], PDO::PARAM_INT);
        if (!$this->_rowStmt->execute()) {
            
            throw new Exception(var_export($this->_rowStmt->errorInfo(), true));
        }
        
        
        return (int)$this->_db->lastInsertId();
    }
    
    /**
     *
     * @param string $documentName имя документа
     * @param string $fileName имя файла, которые мы будем импортировать
     * @param string $dstFileName  имя файла
     */
    public function import($documentName, $fileName, $dstFileName) {
        if (!file_exists($fileName)) throw new Exception("cant find file : $fileName");
            
        $fileId = $this->_createFile($dstFileName);
                
        $this->_phpExcel = PHPExcel_IOFactory::load($fileName);
        
        $documentId = $this->_createDocument($documentName, $fileId);
        if (!$documentId) throw new Exception("cant get documentId for $documentName");

        // бежим по рабочим листам
        foreach ($this->_phpExcel->getWorksheetIterator() as $worksheet) {
            
            
            // получим инфу по объединенным ячейкам
            $mergedCellsRange = $worksheet->getMergeCells();
            $mergedCells = array();
            foreach($mergedCellsRange as $range) {
                 $currMergedCellsArray = PHPExcel_Cell::splitRange($range);
                 
                 $mergedCells[$currMergedCellsArray[0][0]] = $currMergedCellsArray[0];
            }
            
            //var_dump($mergedCellsRange); die();
            
            
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

                    
                    
                    // получем ширину ячейки                        
                    $columnWidth = $worksheet->getColumnDimensionByColumn($col)->getWidth();  //getColumnDimension
                    $columnWidth = $columnWidth - 0.71;  // fix value
                    echo "$columnName.$row width $columnWidth <br/>"; //die();
                        
                        
                        
                    $borderTop = $worksheet->getStyle($columnName.$row)->getBorders()->getTop()->getBorderStyle();
                    echo("border : ");
                    var_dump($borderTop);
                    if ($borderTop == 'medium') $borderTop = 2;
                    elseif ($borderTop == 'thin') $borderTop = 1;
                    else $borderTop = 0;
                    
                    $borderBottom = $worksheet->getStyle($columnName.$row)->getBorders()->getBottom()->getBorderStyle();
                    if ($borderBottom == 'medium') $borderBottom = 2; 
                    elseif ($borderBottom == 'thin') $borderBottom = 1;
                    else $borderBottom = 0;

                    $borderLeft = $worksheet->getStyle($columnName.$row)->getBorders()->getLeft()->getBorderStyle();
                    if ($borderLeft == 'medium') $borderLeft = 2; 
                    elseif ($borderLeft == 'thin') $borderLeft = 1;
                    else $borderLeft = 0;
                    
                    $borderRight = $worksheet->getStyle($columnName.$row)->getBorders()->getRight()->getBorderStyle();
                    if ($borderRight == 'medium') $borderRight = 2; 
                    elseif ($borderRight == 'thin') $borderRight = 1;
                    else $borderRight = 0;


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



                    $color = $this->_phpExcel->getActiveSheet()->getStyle($columnName.$row)->getFill()->getStartColor()->getRGB();
                    $font = $this->_phpExcel->getActiveSheet()->getStyle($columnName.$row)->getFont()->getName();
                    $fontSize = $this->_phpExcel->getActiveSheet()->getStyle($columnName.$row)->getFont()->getSize();
                    $bold = $this->_phpExcel->getActiveSheet()->getStyle($columnName.$row)->getFont()->getBold();

                    $readOnly = false;
                    if ($color != '' && $color != '000000') $readOnly = true;
                    
                    // обработка colspan-ов
                    $colspan = 0;
                    if (isset($mergedCells[$columnName.$row])) {
                        $colFrom = $mergedCells[$columnName.$row][0];
                        $colTo = $mergedCells[$columnName.$row][1];
                        $colFromName = $this->_extractCellName($colFrom);
                        $colToName = $this->_extractCellName($colTo);
                        
                        $colToIndex = PHPExcel_Cell::columnIndexFromString($colToName);
                        $colFromIndex = PHPExcel_Cell::columnIndexFromString($colFromName);
                        $colspan = $colToIndex - $colFromIndex + 1;
                        
                    }
                    
                    //var_dump($this->_phpExcel->getActiveSheet()->getStyle($columnName.$row)->getBorders()->getAllBorders());die();
                    //$border = $this->_phpExcel->getActiveSheet()->getStyle($columnName.$row)->getBorders()->getAllBorders()->getBorderStyle();
                    
             
                    $params = array(
                        'worksheetId' => $worksheetId,
                        'string' => $row,
                        'column' => $columnName,
                        'value' => $val,
                        'readOnly' => $readOnly,
                        'formula' => $formula,
                        'bold' => $bold,
                        'color' => $color,
                        'font' => $font,
                        'fontSize' => $fontSize,
                        'width' => $columnWidth,
                        'colspan' => $colspan,
                        'borderTop' => $borderTop,
                        'borderBottom' => $borderBottom,
                        'borderLeft' => $borderLeft,
                        'borderRight' => $borderRight
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

// 1 - логистика
// 2 - бюджет логистики
// 3 - пиу
try {
    $import = new ExcelImporter();
    //$import->import('example_3', "../media/xls/example_3.xlsx", 'example_3.xls');
    
    //$import->import('Бюджет логистики_02_итог.xls', "../media/xls/logistic.xlsx", 'Бюджет логистики_02_итог.xls');
    //$import->import('Бюджет производства_01_итог.xls', "../media/xls/budget.xlsx", 'Бюджет производства_01_итог.xls');
    //$import->import('Бюджет производства_02_исправленный.xls', "../media/xls/d7.xlsx", 'Бюджет производства_02_исправленный.xls');
    //$import->import('ПиУ для Правления.xls', "../media/xls/d9.xlsx", 'ПиУ для Правления.xls');
    //$import->import('Сводный бюджет_01_итог.xls', "../media/xls/d25.xlsx", 'Сводный бюджет_01_итог.xls');
    $import->import('Сводный бюджет_02_v23.xls', "../media/xls/d1.xlsx", 'Сводный бюджет_02_v23.xls');
    
    
    
    
    
    
    //$import->import('Сводный бюджет', "../media/xls/svodniy.xlsx", 'Сводный бюджет.xls');
} catch (Exception $exc) {
    echo 'Exception : '.$exc->getMessage();
}


echo(time());




?>