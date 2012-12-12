<?php

class ImportMailPhrases {
    
    protected $filename = "media/xls/mail_themes_and_pharses.csv";
    
    protected $mail_codes = array();
    
    protected $data = array();
    
    protected $old_data = array();
    
    protected $phrases = array();
    
    protected $for_insert = array();
    
    protected $not_modifated = array();
    
    protected $count_insert = 0;
    
    protected $system = array(array('name'=>'.', 'code'=>'SYS'), 
                              array('name'=>',', 'code'=>'SYS'),
                              array('name'=>':', 'code'=>'SYS'), 
                              array('name'=>'"', 'code'=>'SYS'),
                              array('name'=>'-', 'code'=>'SYS'),
                              array('name'=>';', 'code'=>'SYS'));
    
    protected $transaction;

    public function __construct() {
        
        $this->filename = __DIR__.'/../../'.$this->filename;
        
    }
    
    public function run() {
        $start_time = microtime(true);
        
        try {
        
        $this->parseCSV();
        
        $this->loadOldData(array('name', 'code'));
        foreach (explode(";", $this->data[0]) as $key => $value) {
            $this->mail_codes[] = trim(trim($value, "\r\n"));
        }

        
        unset($this->data[0]);
//        
//        echo '<pre>';
//        var_dump($this->mail_codes);
//        echo '</pre>';
        $this->setPhrases();
        
        foreach ($this->phrases as $index => $search){
            if($this->syncData($index, $search) === false){
                break;
            }
        }
        
        $this->saveData();
        //header("text/html; charset=utf-8");
        
        } catch ( Exception $e ) {
    		
            if($this->transaction != null) {
                $this->transaction->rollback();
            }
    		//$this->transaction->rollback();
    		
    		echo $e->getMessage()." в файле ".$e->getFile()." на строке  ".$e->getLine().'<br>';
    		exit("Обработаное исключение");
    	}
        
        $end_time = microtime(true);
    	echo "<h3>";
    	echo "Файл - {$this->filename} <br>";
    	echo "Размер - ".(filesize($this->filename)/1024)." Кбайт <br>";
    	echo "Время последнего изменения файла  - ".date("d.m.Y H:i:s.", filemtime($this->filename))." <br>";
    	echo "Время импорта - ". ($end_time - $start_time).' c. <br>';
    	//echo "Количество обработаных строк данных - ".$count_str." по ".$count_col.' колонки <br>';
    	echo "Добавлено  ".$this->count_insert." записей <br>";
    	if(!empty($this->for_insert)){
                foreach ($this->for_insert as $k => $v){
                    echo "{$v['code']} = {$v['name']} <br>";
                }
                foreach ($this->system as $k => $v){
                    echo "{$v['code']} = {$v['name']} <br>";
                }
    		
    	}
    	echo "Старые данные удалены";
    	
    	echo "</h3>";
    }
    
    public function parseCSV() {
        
        if(file_exists($this->filename)){
            $this->data = file($this->filename);
        }else{
            throw new Exception("Файл {$this->filename} не найден!");
        }
        
    }
    
    public function loadOldData($rows) {
        $yii_rows = implode(',', $rows);
        $this->old_data = Yii::app()->db->createCommand()
	    		->select( $yii_rows )
	    		->from( 'mail_phrases' )
	    		->queryAll();
//        echo '<pre>';
//        var_dump($this->old_data);
//        echo '</pre>';
//        exit();
    }
    
    public function setPhrases() {
        
        foreach($this->data as $k => $line){
           $words = explode(";", $line);
           foreach ($this->mail_codes as $k => $code) {
               if(isset($words[$k])){
                   if(!empty($words[$k]) AND $words[$k] != "\r\n") {
                       $this->phrases[] = array('code'=>$code, 'name'=>trim(trim($words[$k]), "\r\n"));
                   }
               } else {
                   throw new Exception("Была ошибка при импорте в CSV или другая ошибка!");
               }
           }
        }
    }
    
    public function syncData($index, $search) {
        
        if(!empty($this->old_data)) {
            $is_found = false;
            foreach ($this->old_data as $key => $value) {
                if($value["code"] == $search["code"] AND $value["name"] == $search["name"]){
                    $this->not_modifated[] = $this->old_data[$key];
                    unset($this->old_data[$key]);
                    $is_found = true;
                    break;
                }else{
                    //echo strlen($value["code"]) .' = '. strlen($search["code"])."  => {$search['code']} {$value['name']} => {$search['name']}";
                    $is_found = false;
//                    file_put_contents("media/xls/debug.txt", $search["code"]);
//                echo '<pre>';
//                var_dump(strlen($value["code"]) == strlen($search["code"]));
//                echo '</pre>';
//                exit();
                }
            }
            if(!$is_found) {
                 $this->for_insert[] = $search;   
            }else{
                $this->for_insert[] = $search;//Всеравно удаляем!
            }
            unset($this->phrases[$index]);
//        echo '<pre>';
//        var_dump($this->for_insert);
//        echo '</pre>';
//        exit();
        }else{
                return false;
            }
        return true;
    }
    
    public function saveData() {
        
        $command = Yii::app()->db->createCommand();
        
        $this->transaction = Yii::app()->db->beginTransaction();
    
        $command->delete("mail_phrases");
        
        foreach($this->for_insert as $k => $row) {
            $this->count_insert++;
            $command->insert("mail_phrases", array(
                'code' => $row['code'],
                'name' => $row['name']
            ));
        }
        
        foreach($this->system as $k => $row) {
            $this->count_insert++;
            $command->insert("mail_phrases", array(
                'code' => $row['code'],
                'name' => $row['name']
            ));
        }
        
        $this->transaction->commit();
    		
    	
   
    }
   
}

