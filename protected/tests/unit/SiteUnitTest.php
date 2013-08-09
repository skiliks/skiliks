<?php

class SiteUnitTest extends CDbTestCase
{
    public function testGoogleAnalyticsExists()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped();
        }

        $url = Yii::app()->params['frontendUrl'];
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handler);

        $this->assertFalse(empty($response));
        $this->assertContains('google-analytics.com/analytics.js', $response);
    }
} 