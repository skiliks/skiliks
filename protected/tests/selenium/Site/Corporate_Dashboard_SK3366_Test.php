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

        $this->open('/ru');

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

        $vacancyName ="vacancyName";
        $vacancyName .=  (string)rand(100, 300)+(string)rand(20,50)-(string)rand(10,30);

        //пустые
        $this->addVacancy("xpath=//*[@id='Vacancy_professional_occupation_id']/option[1]","xpath=//*[@id='Vacancy_position_level_slug']/option[1]","xpath=//*[@id='Vacancy_position_level_slug']/option[1]","","",array("Выберите профессиональную область", "Выберите уровень позиции", "Выберите специализацию", "Введите название позиции"));

        // выбрано все, но название не введено
        $this->addVacancy("xpath=//*[@id='Vacancy_professional_occupation_id']/option[2]","xpath=//*[@id='Vacancy_position_level_slug']/option[2]","xpath=//*[@id='Vacancy_position_level_slug']/option[2]",$vacancyName,"jdufd",array("Не является правильным URL"));

        // все правильно и добавлено
        $this->addVacancy("xpath=//*[@id='Vacancy_professional_occupation_id']/option[2]","xpath=//*[@id='Vacancy_position_level_slug']/option[2]","xpath=//*[@id='Vacancy_position_level_slug']/option[2]",$vacancyName,"www.skiliks.com",array($vacancyName));

        // все тоже, ошибка что нельзя одинаковую вакансию добавить
        $this->addVacancy("xpath=//*[@id='Vacancy_professional_occupation_id']/option[2]","xpath=//*[@id='Vacancy_position_level_slug']/option[2]","xpath=//*[@id='Vacancy_position_level_slug']/option[2]",$vacancyName,"www.skiliks.com",array("Такое название уже используется"));

        // меняем имя и убираем ссылку
        $this->addVacancy("xpath=//*[@id='Vacancy_professional_occupation_id']/option[2]","xpath=//*[@id='Vacancy_position_level_slug']/option[2]","xpath=//*[@id='Vacancy_position_level_slug']/option[2]",$vacancyName."sd","www.skiliks.com",array($vacancyName."sd"));
    }

    public function addVacancy($field, $positionLevel, $specialization, $position, $url, $errors)
    {
        $this->optimal_click("xpath=//*[@id='Vacancy_professional_occupation_id']/option[1]");
        $this->mouseOver($field);
        $this->optimal_click($field);

        $this->optimal_click("xpath=//*[@id='Vacancy_position_level_slug']/option[1]");
        $this->mouseOver($positionLevel);
        $this->optimal_click($positionLevel);

        $this->optimal_click("xpath=//*[@id='Vacancy_professional_specialization_id']/option[1]");
        $this->mouseOver($specialization);
        $this->optimal_click($specialization);

        $this->type("сss=#Vacancy_label",$position);

        $this->type("сss=#Vacancy_link",$url);

        $this->optimal_click("css=.send-vacancy");

        for ($i=0; $i<count($errors); $i++ )
        {
            sleep(1);
            $this->waitForVisible("xpath=(//*[contains(text(),'".$errors[$i]."')])");
        }
    }

    /**
     * test_Corporate_Dashboard_Invites_SK3366() тестирует задачу SKILIKS-3366
     */
    public function test_Corporate_Dashboard_Invites_SK3366()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();

        $this->clear_blocked_auth_users();

        $this->open('/ru');

        $this->optimal_click(Yii::app()->params['test_mappings']['site']['logIn']);
        $this->waitForVisible(Yii::app()->params['test_mappings']['site']['username']);
        $this->type(Yii::app()->params['test_mappings']['site']['username'],'selenium.engine@skiliks.com');
        $this->type(Yii::app()->params['test_mappings']['site']['userpass'],'skiliks123123');
        $this->optimal_click(Yii::app()->params['test_mappings']['site']['enter']);

        $this->waitForVisible(Yii::app()->params['test_mappings']['corporate']['username']);
        $this->assertTrue($this->getText(Yii::app()->params['test_mappings']['corporate']['username'])=="seleniumEngine");

    }
}