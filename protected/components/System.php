<?php

/**
 * Description of System
 *
 * @author ivan
 */
class System 
{
    public static function classToUrls($classes) 
    {        
        $links = array();
        $pre = 'action';
        $pos = 'Controller';
        $path = $_SERVER['HTTP_HOST'].'/index.php/';
        foreach ($classes as $classname) {
            
            include_once __DIR__.'/../controllers/'.$classname.'.php';
            $reflection = new ReflectionClass($classname);
            $methods = $reflection->getMethods();
            
            $controller = substr($classname, 0, strlen($classname)-strlen($pos));
            foreach ($methods as $method) {
                
                if($method->class == $classname) {
                    
                    if($pre == substr($method->name, 0, strlen($pre))) {
                        $action = substr($method->name, strlen($pre));
                        $links[] = array('href'=>($path.$controller.'/'.$action), 'title'=>($controller.'/'.$action));
                    }
                }
            }
        }
        
        return $links;
        
    }
    
    /**
     * Отображение комбика в виде html
     * 
     * @param type $tableName
     * @param type $nameField
     * 
     * @return string 
     */
    public static function getComboboxHtml($tableName, $nameField = 'title', $condition='', $idField = 'id') 
    {
        $sql = "select * from {$tableName} ".$condition;
        
        $dataReader = Yii::app()->db
            ->createCommand($sql)
            ->query();
        
        $html = "<select><option value=\'-1\'>Все</option>";
        foreach($dataReader as $row) { 
            $html .= "<option value='{$row[$idField]}'>{$row[$nameField]}</option>";
        }
        $html .= '</select>';
        
        return $html;
    }
    
    /**
     * @param string $tableName
     * @param string $nameField
     * @param string $condition
     * @param string $firstValue
     * @param integer $idField
     * 
     * @return mixed array
     */
    public static function getComboboxData($tableName, $nameField = 'title', $condition='', $firstValue = false, $idField = 'id') 
    {
        $dataReader = Yii::app()->db
            ->createCommand("select * from {$tableName} ".$condition)
            ->query();
            
        $records = array();
        
        if ($firstValue) {
            $records[] = '-1:'.$firstValue;
        }
        
        foreach($dataReader as $row) { 
            $records[] = $row[$idField].':'.$row[$nameField];
        }
        
        return implode(';', $records);
    }
    
    /**
     * @return mixed array
     */
    public static function getBehavioursListForAdminka()
    {
        $list = array();
        
        foreach ($behaviours as $behaviour) {
            $list[] = array(
                'id'    => $behaviour->id,
                'cell'  => array(
                    $behaviour->id, 
                    (NULL !== $behaviour->laerning_goal) ? $behaviour->laerning_goal->title : '--',
                    $behaviour->code,
                    $behaviour->title, 
                    $behaviour->scale
                )
            );
        }
        
        return $list;
    }
}