<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 29.11.12
 * Time: 21:07
 * To change this template use File | Settings | File Templates.
 */
class CacheTest extends CDbTestCase
{
    public function test_cache() {
        $key = 'test' . time();
        Yii::app()->cache->set($key, 'ok');
        $this->assertEquals('ok', Yii::app()->cache->get($key));

    }

}
