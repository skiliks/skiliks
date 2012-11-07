<?php

class RegistrationTest extends CWebTestCase
{
    public static $browsers = array(
        array(
            'name'    => 'Firefox',
            'browser' => '*firefox',
            'port'    => 4444,
            'timeout' => 50000,
        ),
        array(
            'name'    => 'Chrome',
            'browser' => '*googlechrome',
            'port'    => 4444,
            'timeout' => 50000,
        )
    );
    protected function setUp()
    {

        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testRegistration() {
        $this->open('/');
        $this->waitForXpathCount("//input[@value='Регистрация']", 1);
        $this->click("//input[@value='Регистрация']");
        $this->type('id=email', 'andrey' . time() . '@kostenko.name');
        $this->type('id=pass1', 'test');
        $this->type('id=pass2', 'test');
        $this->click('css=input.btn');
    }

}
