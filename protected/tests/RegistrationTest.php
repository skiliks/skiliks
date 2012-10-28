<?php

class RegistrationTest extends CWebTestCase
{
    protected function setUp()
    {
        $this->setBrowser('*firefox');
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
