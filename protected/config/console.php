<?php
return CMap::mergeArray(
    require(dirname(__FILE__) . '/main.php'),
    array('commandMap' => array(
        'migrate' => array(
            'class' => 'system.cli.commands.MigrateCommand',
            'interactive' => false
        )),
    )
);