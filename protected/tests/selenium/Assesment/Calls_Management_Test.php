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

        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['settings']);
        $this->optimal_click("css=.volume-control.control-phone.volume-on");
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['settings']);
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['close']);

        $this->run_event('ET1.1', Yii::app()->params['test_mappings']['active_icons']['active_phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, помню про бюджет. Сейчас же приступаю к доработке')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, за три часа управлюсь')])");
        sleep(2);
        $this->run_event('ET2.1', Yii::app()->params['test_mappings']['active_icons']['active_phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Валерий Семенович,  так в прошлый раз нам пришлось презентацию за день делать!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, у меня в графике уже выделено время на проверку')])");
        sleep(2);
        $this->run_event('ET3.3', Yii::app()->params['test_mappings']['active_icons']['active_phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Здравствуйте. Любопытно')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Извините, давайте созвонимся после отпуска, я сейчас очень занят')])");
        sleep(2);
        $this->run_event('ET9', Yii::app()->params['test_mappings']['active_icons']['active_phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, вопрос в чем?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, давайте ближе к делу!')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, так какое у вас ко мне дело?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Василий, прошу прощения, вы к чему все это рассказываете?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Та-а-ак, и что же в вашем бюджете изменилось?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Принимаю, что же с вами делать. Сейчас посмотрю и внесу изменения. ')])");
        sleep(2);
        $this->run_event('ET10', Yii::app()->params['test_mappings']['active_icons']['active_phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас найду и перешлю')])");
        sleep(2);
        $this->run_event('ET11', Yii::app()->params['test_mappings']['active_icons']['active_phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Раиса Романовна, приношу извинения')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Слушаюсь, Раиса Романовна, сейчас сделаю.')])");
        sleep(2);
        $this->run_event('TT7.1.1', Yii::app()->params['test_mappings']['active_icons']['active_phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Ладно, я понял')])");
        sleep(2);
        $this->run_event('RS2',"xpath=(//*[contains(text(),'Приветствую, Егор!  У тебя что-то срочное?')])",'click');
        $this->optimal_click("xpath=(//*[contains(text(),'Егор, я начну работу по проекту только после отпуска')])");
        sleep(2);
        $this->clearEventQueueBeforeEleven('RST3');
        sleep(2);
        $this->clearEventQueueBeforeEleven('RST4');
        sleep(2);
        $this->clearEventQueueBeforeEleven('RST5');
        sleep(2);
        $this->clearEventQueueBeforeEleven('RST9');
        sleep(2);
        $this->run_event('RST8', Yii::app()->params['test_mappings']['active_icons']['active_phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Добрый день, а вы по какому вопросу?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Да, я отвечаю за итоговую версию бюджета.')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Так в чем же проблема')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Давайте на цифрах посмотрим, серьезно ли расходится')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Я попробую. Поговорю с вашим руководителем.')])");
        sleep(5);
        $this->run_event('RST10',Yii::app()->params['test_mappings']['active_icons']['active_phone'],'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Привет, Петр. У тебя что-то срочное?')])");
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, понял тебя, спасибо! Значит, с работы выходить')])");
        sleep(2);
        $this->run_event('ET3.1', Yii::app()->params['test_mappings']['active_icons']['active_phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, Иван')])");
        sleep(2);
        $this->run_event('ET12.1', Yii::app()->params['test_mappings']['active_icons']['active_phone'], 'click');
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
        $this->optimal_click("xpath=(//*[contains(text(),'Хорошо, сейчас перешлю')])");
        sleep(2);
        $this->simulation_showLogs();
        $this->waitForVisible(Yii::app()->params['test_mappings']['log']['group_3_3']);
        $this->assertText(Yii::app()->params['test_mappings']['log']['group_3_3'],"100.00");
    }
}
