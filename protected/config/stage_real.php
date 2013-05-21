<?php

return CMap::mergeArray(require(dirname(__FILE__) . '/base.php'), array(
    'preload'=> array('log', 'RSentryException'),
    'components'=>array(
        'RSentryException'=> array(
            'dsn'=> 'https://bfd7395024f24728afdf79e9034bca04:2f8bec2e2c40493dbf7b07db88afc94f@app.getsentry.com/4572',
            'class' => 'application.components..yii-sentry-log.RSentryComponent',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'application.components.yii-sentry-log.RSentryLog',
                    'dsn'=> 'https://bfd7395024f24728afdf79e9034bca04:2f8bec2e2c40493dbf7b07db88afc94f@app.getsentry.com/4572',
                    'levels'=>'error, warning',
                ),
            ),
        ),
        'db' => array(
            'connectionString' => 'mysql:host=db1.skiliks.com;dbname=skiliks',
            'emulatePrepare' => true,
            'username' => 'skiliks',
            'password' => 'dep-vep-eb-up-a',
            'charset' => 'utf8',
            'persistent'=>true,
            'enableParamLogging' => true,
            'enableProfiling' => true
        ),
    ),
    'params' => array(
        'frontendUrl' => 'http://front.skiliks.com/',

        'public' => [
            'skiliksSpeedFactor' => 6,
            'simulationStartTime' => '9:45',
        ],
        'zoho' => array(
            'saveUrl'             => 'http://new.skiliks.com/zoho/saveExcel',
            'apiKey'              => 'b5e3f7316085c8ece12832f533c751be',
        ),
    )
));


