<?php
/**
 * Base app config
 */
defined('YII_ENABLE_ERROR_HANDLER') or define('YII_ENABLE_ERROR_HANDLER', true);
defined('YII_ENABLE_EXCEPTION_HANDLER') or define('YII_ENABLE_EXCEPTION_HANDLER', true);
return array(
    'import' => array(
        'application.models.*',
        'application.models.Flags.*',
        /*'application.models.Characters.*',
        'application.models.DayPlan.*',
        'application.models.Dialog.*',
        'application.models.Events.*',
        'application.models.Excel*',
        'application.models.FlagsRules.*',
        'application.models.Mail.*',
        'application.models.MyDocuments.*',
        'application.models.Simulation.*',
        'application.models.User.*',*/
        'application.components.*',
        'application.components.Zoho.*',
        /*'application.components.DayPlan.*',
        'application.components.Dialog.*',
        'application.components.Excel.*',
        'application.components.Mail.*',*/
    ),
    'components' => array(
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info, trace, log',
                ),

            ),
        ),
        'cache' => array(
            'class' => 'CDbCache',
            'connectionID' => 'db',
            'autoCreateCacheTable' => false
        ),
        'session' => array(
            'class' => 'CDbHttpSession',
            'autoCreateSessionTable' => false,
            'connectionID' => 'db',
            'sessionName' => 'sid'

        ),
        'user' => array(
            'allowAutoLogin' => true,
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            #'showScriptName' => false,

            'rules' => array(

                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
    ),
    'basePath' => dirname(__FILE__) . '/..',


    'preload' => array('log'),

    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        'frontendUrl' => 'http://front.skiliks.loc/',
        'skiliksSpeedFactor' => 8
    ),
);


