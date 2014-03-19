<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Тесты для тестирования страниц сайта, которые доступны незарегистрированным пользователям
 */
class URL_forNoRegisteredUser_Test extends SeleniumTestHelper
{
    public function test_for_no_registered_user()
    {
        $buttons_text = array('Главная','О нас','О продукте','Главная', 'О нас', 'О продукте', 'Вход');
        $text_inside= array('Простой','Познакомьтесь','Мы создали', 'Простой','Познакомьтесь','Мы создали','Запомнить меня');
        $buttons_xpath = array ("xpath=//*[@id='yw1']/li[1]/a", "xpath=//*[@id='yw1']/li[2]/a", "xpath=//*[@id='yw1']/li[3]/a", "xpath=//*[@id='yw2']/li[1]/a", "xpath=//*[@id='yw2']/li[2]/a", "xpath=//*[@id='yw2']/li[3]/a", "xpath=//*[@id='yw0']/li[4]/a");
        $all_buttons = array($buttons_xpath, $buttons_text, $text_inside);

        $buttons_text_en = array('Home','About Us','Product','Home', 'About Us', 'Product');
        $text_inside_en= array('Easy','Meet','About the Product', 'Easy','Meet','About the Product');
        $buttons_xpath_en = array ("xpath=//*[@id='yw1']/li[1]/a", "xpath=//*[@id='yw1']/li[2]/a", "xpath=//*[@id='yw1']/li[3]/a", "xpath=//*[@id='yw2']/li[1]/a", "xpath=//*[@id='yw2']/li[2]/a", "xpath=//*[@id='yw2']/li[3]/a", "xpath=//*[@id='yw1']/li[3]/a");
        $all_buttons_en = array($buttons_xpath_en, $buttons_text_en, $text_inside_en);

        $this->deleteAllVisibleCookies();
        $this->windowMaximize();

        $this->clear_blocked_auth_users();

        $this->open('/ru');

        $this->check_all_urls($all_buttons, "English");

        // проверка наличия попапа Входа
        $this->optimal_click($all_buttons[0][sizeof($all_buttons[0])-1]); // кликаем на кнопку по xpath
        sleep(5);
        $this->assertTextPresent($all_buttons[2][sizeof($all_buttons[0])-1]); // проверяем, что есть особый текст

        $this->optimal_click("xpath=//div[2]/div[1]/a/span"); //локально 3й див

        $this->optimal_click("css=.locator-logo-head");
        $this->optimal_click("xpath=(//*[contains(text(),'English')])");
        sleep(5);

        $this->optimal_click("//*[@id='action-subscribe-form']/div/input[2]");
        sleep(1);
        $this->assertTextPresent("Enter your email address");

        $new_email = "test-email+";
        $new_email .= (string)rand(1, 10000)+(string)rand(1,500);
        $new_email .= "@skiliks.mail";

        $this->type("css=.locator-user-email-value", $new_email);
        $this->optimal_click("xpath=//*[@id='action-subscribe-form']/div/input[2]");
        sleep(10);
        $this->assertTextPresent("Thank");

        $this->optimal_click("css=.locator-logo-head");
        sleep(5);
        $this->type("css=.locator-user-email-value", $new_email);
        $this->optimal_click("xpath=//*[@id='action-subscribe-form']/div/input[2]");
        sleep(1);
        $this->assertTextPresent("has been already added before!");
    }
}