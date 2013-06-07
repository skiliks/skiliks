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
        //$this->markTestIncomplete();

        $this->start_simulation();
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

        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], "10");
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], "08");
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);


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

        $this->run_event('RST1', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->run_event('RS2',"xpath=(//*[contains(text(),'Приветствую, Егор!  У тебя что-то срочное?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Егор, я начну работу по проекту только после отпуска')])");

        $this->run_event('RST3', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->run_event('RST4', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->run_event('RST5', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->run_event('RST6', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'давайте я вам перешлю этот показатель')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Через пять минут данные будут у вас')])");
        sleep(5);

        $this->run_event('RST9', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);

        $this->run_event('ET3.1', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, Иван')])");

        $this->run_event('ET12.1', "css=li.icon-active.phone a", 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас перешлю')])");


        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['show_logs']);
        $this->waitForVisible("id=simulation-points");
        $this->waitForTextPresent('Simulation points');
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['calls6'],"100");
        $this->assertText(Yii::app()->params['test_mappings']['log']['calls6'],"100");
        $this->close();
    }
}
