<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 15.11.12
 * Time: 16:52
 * To change this template use File | Settings | File Templates.
 */

class SeleniumTestCase extends CWebTestCase
{
    /**
     * @var array list of checked browsers
     */
    public static $browsers = array(
        array(
            'name' => 'Firefox',
            'browser' => '*firefox',
            'port' => 4444,
            'timeout' => 50000,
        ),
        array(
            'name' => 'Chrome',
            'browser' => '*googlechrome',
            'port' => 4444,
            'timeout' => 50000,
        )
    );

    protected function setUp()
    {
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }



}
