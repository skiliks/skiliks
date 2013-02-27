<?php
/**
 * Created by JetBrains PhpStorm.
 * User: tania
 * Date: 2/22/13
 * Time: 5:58 PM
 * To change this template use File | Settings | File Templates.
 */
class DialogTest extends CWebTestCase
{
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl(Yii::app()->params['frontendUrl']);
        parent::setUp();
    }

    public function testE2_4_2_9() {
        // next line for not running the test
        $this->markTestIncomplete();
        $this->deleteAllVisibleCookies();
        $this->open('/site/');
        $this->setSpeed("1000");
        $this->waitForVisible('id=login');
        $this->type("id=login", "asd");
        $this->type("id=pass", "123");
        $this->click("css=input.btn.btn-primary");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("xpath=//input[@value='Начать симуляцию developer']")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->click("xpath=//input[@value='Начать симуляцию developer']");
        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("id=addTriggerSelect")) break;
            } catch (Exception $e) {}
            sleep(1);
        }
        $this->type("id=addTriggerSelect", "E2");
        $this->click("css=input.btn.btn-primary");
        sleep(15);
        $this->click("css=a.replica-select");
        sleep(15);
        $this->click("link=— Да, прямо сейчас проконтролирую, как идет подготовка.");
        $this->click("link=— Марина, срочно пересылай мне презентацию для Генерального! Босс сам звонил и интересовался!");
        $this->click("link=— Давай мы все-таки посмотрим, что у тебя там получается с учетом требований Босса. Шли мне презентацию прямо сейчас.");
        $this->click("link=— Ясно, ты не успеваешь…Придется перенести встречу с Боссом на завтра. Уж точно лучше, чем краснеть у него на ковре.");
        sleep(15);
        $this->click("link=— Еще раз, добрый день, Валерий Семенович!");
        $this->click("link=— Валерий Семенович, прошу меня извинить, но обстоятельства сильнее меня! Мы не успеем представить презентацию сегодня.");
        $this->click("//a[contains(text(),'— Простите,  Валерий Семенович, но если бы наш отдел персонала быстрее подбирал нужных людей, я бы справился и с большей нагрузкой. А так многое приходится делать самому. Естественно, что я не успеваю.')]");
        $this->click("css=input.btn.btn-simulation-stop");
        sleep(15);
        $this->click("css=input.btn.logout");
    }
}

