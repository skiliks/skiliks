<?php
return CMap::mergeArray(
    require(dirname(__FILE__) . '/base.php'),

    array(
        'params' => array(
            'frontendUrl' => 'http://new.skiliks.com/'
        )
    )
);