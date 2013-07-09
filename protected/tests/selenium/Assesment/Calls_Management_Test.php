<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * 100% по Эффективное управлению звонками (Область обучения №6)
 */
class Calls_Management_Test extends SeleniumTestHelper
{
    public function test_Dialogs_for_SK2420_3()
    {
        $this->start_simulation();

        $this->clearEventQueueBeforeEleven('RST1');

        $this->run_event('ET1.1', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, помню про бюджет. Сейчас же приступаю к доработке')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, за три часа управлюсь')])");

        $this->run_event('ET2.1', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Валерий Семенович,  так в прошлый раз нам пришлось презентацию за день делать!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, у меня в графике уже выделено время на проверку')])");

        $this->run_event('ET3.3', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Здравствуйте. Любопытно')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Извините, давайте созвонимся после отпуска, я сейчас очень занят')])");

        $this->run_event('ET9', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, вопрос в чем?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, давайте ближе к делу!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, так какое у вас ко мне дело?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, прошу прощения, вы к чему все это рассказываете?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Та-а-ак, и что же в вашем бюджете изменилось?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Принимаю, что же с вами делать. Сейчас посмотрю и внесу изменения. ')])");

        $this->run_event('ET10', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас найду и перешлю')])");

        $this->run_event('ET11', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, приношу извинения')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Слушаюсь, Раиса Романовна, сейчас сделаю.')])");

        $this->run_event('TT7.1.1', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно, я понял')])");

        $this->run_event('RS2',"xpath=(//*[contains(text(),'Приветствую, Егор!  У тебя что-то срочное?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Егор, я начну работу по проекту только после отпуска')])");

        $this->clearEventQueueBeforeEleven('RST3');

        $this->clearEventQueueBeforeEleven('RST4');

        $this->clearEventQueueBeforeEleven('RST5');

        $this->run_event('RST6', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'давайте я вам перешлю этот показатель')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Через пять минут данные будут у вас')])");
        sleep(5);

        $this->clearEventQueueBeforeEleven('RST9');

        $this->run_event('ET3.1', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, Иван')])");

        $this->run_event('ET12.1', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас перешлю')])");

        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible("id=simulation-points");
        $this->waitForTextPresent('Simulation points');
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['calls6'],"75");
        $this->assertText(Yii::app()->params['test_mappings']['log']['calls6'],"75");
        $this->close();
    }
}
