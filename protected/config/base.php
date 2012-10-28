<?php
/**
 * Base app config
 */


define('SKILIKS_SPEED_FACTOR', 8);

return array(
    'import'=>array(
        'application.models.*',
        'application.components.*'
    ),
    'components'=>array(
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
