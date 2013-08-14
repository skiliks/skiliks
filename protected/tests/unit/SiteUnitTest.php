<?php

class SiteUnitTest extends CDbTestCase
{
    public function testGoogleAnalyticsExists()
    {
        if (!extension_loaded('curl')) {
            $this->markTestSkipped();
        }

        $url = 'http://skiliks.com';
        $handler = curl_init($url);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handler);

        $this->assertFalse(empty($response));
        $this->assertContains("(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');", $response);
        $this->assertContains("ga('create', 'UA-42053049-1', 'skiliks.com')", $response);
        $this->assertContains("ga('send', 'pageview');", $response);
        $this->assertContains('google-analytics.com/analytics.js', $response);
    }
} 