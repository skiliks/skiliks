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
    
    public function run() 
    {
        $start_time = microtime(true);
        
        try {
        
            $this->parseCSV();

            $this->loadOldData(array('name', 'code'));
            foreach (explode(";", $this->data[0]) as $key => $value) {
                $this->mail_codes[] = trim(trim($value, "\r\n"));
            }

            unset($this->data[0]);

            $this->setPhrases();

            foreach ($this->phrases as $index => $search){
                if($this->syncData($index, $search) === false){
                    break;
                }
            }

            $this->saveData();
        
        } catch ( Exception $e ) {
    		
            if($this->transaction != null) {
                $this->transaction->rollback();
            }
            
            return array(
                'status' => false,
                'text'   => $e->getMessage()." в файле ".$e->getFile()." на строке  ".$e->getLine().'<br>',
            );
    		
    	}
        
        $end_time = microtime(true);
        
        $html = "Добавлено  ".$this->count_insert." фраз.<br>";
        $html .= "Всего кодов  ".count($this->mail_codes)." must be 24 (21 Dec 2012) <br>";
        $html .= "Коды:  ".  implode(', ', $this->mail_codes). ".<br/>";
        
        // skip extra output data
    	/*if(!empty($this->for_insert)){
                foreach ($this->for_insert as $k => $v){
                    $html .= "{$v['code']} = {$v['name']} <br>";
                }
                foreach ($this->system as $k => $v){
                    $html .= "{$v['code']} = {$v['name']} <br>";
                }
    		
    	}*/
    	$html .= "Старые данные удалены.";
    	
    	return array(
            'status' => true,
            'text'   => $html,
        );
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
                    $is_found = false;
                }
            }
            if(!$is_found) {
                 $this->for_insert[] = $search;   
            }else{
                $this->for_insert[] = $search;//Всеравно удаляем!
            }
            unset($this->phrases[$index]);
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

