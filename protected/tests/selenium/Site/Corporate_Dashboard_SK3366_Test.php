<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для тестирования работы корпоративного кабинета - добавление вакансии, фидбек, ссылки, сортировка в таблице, удаление приглашений(для SK3366)
 */
class Corporate_Dashboard_SK3366_Test extends SeleniumTestHelper
{
    /**
     * test_Corporate_Dashboard_Add_Vacancy_SK3366() тестирует задачу SKILIKS-3366
     */
    public function test_Corporate_Dashboard_Add_Vacancy_SK3366()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();

        $this->clear_blocked_auth_users();

        $this->open('http://skiliks:skiliks1444@test.skiliks.com/ru');

        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site']['username']);
        $this->type(Yii::app()->params['test_mappings']['site']['username'],'selenium.engine@skiliks.com');
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],'skiliks123123');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);

        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['username'])=="seleniumEngine");

        //add vacancies

        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['addVacancy']);
        $this->waitForVisible("css=.send-vacancy");

        $this->pause(5*1000);

        $vacancyName ="vacancyName";
        $vacancyName .=  (string)rand(100, 300)+(string)rand(20,50)-(string)rand(10,30);

        //пустые
        echo ' - 1';
        $this->pause(5*1000);
        $this->addVacancy(null,null,null,"","",array("Выберите профессиональную область", "Выберите уровень позиции", "Выберите специализацию", "Введите название позиции"));

        echo ' - 2';
        // выбрано все, но название не введено
        $this->addVacancy("css=.option-2:eq(1)","css=.option-2:eq(2)","css=.option-2:eq(3)",$vacancyName,"jdufd",array("Не является правильным URL"));

        echo ' - 3';
        // все правильно и добавлено
        $this->addVacancy("css=.option-2:eq(1)","css=.option-2:eq(2)","css=.option-2:eq(3)",$vacancyName,"www.skiliks.com",[]); // array($vacancyName)

        $this->optimal_click(Yii::app()->params['test_mappings']['corporate']['addVacancy']);
        $this->waitForVisible("css=.send-vacancy");

        $this->pause(5*1000);

        echo ' - 4';
        // все тоже, ошибка что нельзя одинаковую вакансию добавить
        $this->addVacancy("css=.option-2:eq(1)","css=.option-2:eq(2)","css=.option-2:eq(3)",$vacancyName,"www.skiliks.com",array("Такое название уже используется"));

        echo ' - 5';
        // меняем имя и убираем ссылку
        $this->addVacancy("css=.option-2:eq(1)","css=.option-2:eq(2)","css=.option-2:eq(3)",$vacancyName."sd","www.skiliks.com",[]); // array($vacancyName."sd")
    }

    public function addVacancy($field, $positionLevel, $specialization, $position, $url, $errors)
    {
        if (null != $field) {
            $this->optimal_click("css=.sbToggle:eq(2)");
            $this->mouseOver($field);
            $this->optimal_click($field);
        }

        if (null != $field) {
            $this->optimal_click("css=.sbToggle:eq(3)");
            $this->mouseOver($positionLevel);
            $this->optimal_click($positionLevel);
        }

        if (null != $field) {
            $this->optimal_click("css=.sbToggle:eq(4)");
            $this->mouseOver($specialization);
            $this->optimal_click($specialization);
        }

        $this->type("id=Vacancy_label",$position);

        $this->type("id=Vacancy_link",$url);

        $this->optimal_click("css=.send-vacancy");

        $this->pause(5*1000);

        for ($i=0; $i<count($errors); $i++ )
        {
            sleep(1);
            $this->waitForVisible("xpath=(//*[contains(text(),'".$errors[$i]."')])");
        }
    }
}