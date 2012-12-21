<?php

/**
 * Description of System
 *
 * @author ivan
 */
class System {
    //put your code here
    public static function classToUrls($classes) {
        
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
}
