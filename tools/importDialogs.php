<?php
/**
 * Импорт диалогов
 */

header("HTTP/1.0 200 OK");
header('Content-type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Access-Control-Allow-Origin: *");

set_time_limit(0);

require_once '../lib/excel/PHPExcel/IOFactory.php';

/**
 * Импорт диалогов
 */
class DialogImport {
    
    protected $_db;

    protected $_phpExcel;
    
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
    
    public function import($fileName) {
        if (!file_exists($fileName)) throw new Exception("cant find file : $fileName");
            
        
                
        $this->_phpExcel = PHPExcel_IOFactory::load($fileName);
        //$wsNames = $this->_phpExcel->getSheetNames();
        //var_dump($wsNames);
    }
}

$import = new DialogImport();
$import->import('../media/xls/scenario.xlsx');

?>
