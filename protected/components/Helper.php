<?php

/**
 * @author slavka
 */
class Helper 
{
    public static function callAction($controller_class, $action)
    {
        include_once __DIR__.'/../controllers/'.$controller_class.'.php';
        
        $controller = new $controller_class($controller_class);
        
        ob_start();
            $controller->is_test = true;
            $controller->$action();
            $result = ob_get_contents();
        ob_clean();
        
        return $result;    
    }
}

