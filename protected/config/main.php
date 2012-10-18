<?php

define('SKILIKS_SPEED_FACTOR', 8);

return array(
    'import'=>array(
        'application.models.*',
        'application.components.*'
    ),
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=skiliks',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            
             'enableParamLogging'=>true,
            'enableProfiling'=>true
        ),
        
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning, info, trace, log',
                    
                     
                    
                ),
                
            ),
        )
    ),
    
    
    'preload'=>array('log'),
    
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params'=>array(
        'frontendUrl'=>'http://front.skiliks.loc/',
    )
);    

?>
