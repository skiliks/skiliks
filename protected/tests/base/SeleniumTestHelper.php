<?php
/**
 * \addtogroup Selenium
 * @{
 */
/**
 * Класс с методами для Selenium Test(-ов)
 */
class SeleniumTestHelper extends CWebTestCase
{
    /**
     * start_simulation - это метод, который включает стандартные действия при начале симуляции
     * от открытия окна браузера до самого входа в dev-режим
     */
    public function start_simulation()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/');
        $this->optimal_click("xpath=//header/nav/a[4]");
        $this->waitForVisible("xpath=//div[1]/form/div[1]/input");
        $this->type("xpath=//div[1]/form/div[1]/input", "tatiana@skiliks.com");
        $this->type("xpath=//div[1]/form/div[2]/input", "123123");
        $this->click("xpath=//div[1]/form/div[5]/input");
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
    }

    /**
     * run_event - это метод для запуска события по его Event_code
     */
    public function run_event($event)
    {
        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "$event");
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['event_create']);
    }

    /**
     * call_phone - это метод для звонка по телефону, когда телефон не активен (иконка не движется)
     */
    public function call_phone ($whom, $theme)
    {
        $this->optimal_click(Yii::app()->params['test_mappings']['icons']['phone']);
        $this->waitForElementPresent(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->mouseOver(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->click(Yii::app()->params['test_mappings']['phone']['contacts_list']);
        $this->waitForElementPresent($whom);
        $this->mouseOver($whom);
        $this->click($whom);
        $this->waitForElementPresent($theme);
        $this->mouseOver($theme);
        $this->click($theme);
    }

    /**
     * reply_call - это метод для ответа на входящий звонок, когда телефон активен (иконка движется)
     */
    public function reply_call ()
    {
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
    }

    /**
     * no_reply_call - это метод для игнора входящего звонка, когда телефон активен (иконка движется)
     */
    public function no_reply_call ()
    {
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
    }

    /**
     * write_mail_active - это метод для создания письма, когда мейл-клиент активен (иконка мигает)
     */
    public function write_mail_active()
    {
        $this->optimal_click("css=li.icon-active.mail a");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
    }

    /**
     * optimal_click - это метод для корректного нажатия (ожидание элемента и только потом нажатие)
     */
    public function optimal_click ($loc)
    {
        $this->waitForVisible($loc);
        $this->click($loc);
    }

    /**
     * how_much_time - это метод для определения поточного игрового времени
     * метод возвращает массив
     */
    public function how_much_time ()
    {
        $time[0] = (int)($this->getText(Yii::app()->params['test_mappings']['time']['hour']));
        $time[1] = (int)($this->getText(Yii::app()->params['test_mappings']['time']['minute']));
        return $time;
    }

    /**
     * transfer_time - это метод для переноса времени на differ минут.
     * Метод стоит использовать для коректного изменения времени для выполнения событий,
     * которые должны происходить с задержкой, где differ -это колличество минут задежки
     */
    public function transfer_time ($differ)
    {
        $time_array=$this->how_much_time(); //запускаем определение текущего времени
        $time_array[1]=$time_array[1]+$differ;  // к минутам приплюсовываем необходимую разницу времени
        if ($time_array[1]>=60) // проверяем выходим ли мы за рамки по минутам
        {
                                              // если выходим за рамки 60 минут, то
            $time_array[0]=$time_array[0]+1;  // увеличиваем количество часов на 1
            $time_array[1]=$time_array[1]-60; // изменяем количество минут
        }

        // меняем поточное время
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_hours'], $time_array[0]);
        $this->type(Yii::app()->params['test_mappings']['set_time']['set_minutes'], $time_array[1]);
        $this->click(Yii::app()->params['test_mappings']['set_time']['submit_time']);
        return $time_array;
    }

    /**
     * is_it_done - это метод для проверки выполнения или не выполнения действия (например, для проверки,
     * что телефон не звонит на протяжении 1 реальной минуты)
     * locator - локатор элемента, наличие которого мы проверяем
     * возвращаем true, если произошло событие
     * возвращаем false, если не произошло
     */
    public function is_it_done ($locator)
    {
        $was_done = false;
        for ($second = 0; ; $second++) {
            if ($second >= 60)
            {
                $was_done = false;
                break;
            }
            try {
                if ($this->isVisible($locator))
                {
                    $was_done=true;
                    break;
                }
            } catch (Exception $e) {}
            sleep(1);
        }
        return $was_done;
    }


    /**
     * verify_flag - это метод для проверки, что значение флага num_flag поменялось
     * и соответсвует значению ver_value
     * возвращаем true, если поменялось
     * возвращаем false, если не изменилось
     */
    public function verify_flag ($num_flag, $ver_value)
    {
        $was_changed=false;
        $current_value='0';
        for ($second = 0; ; $second++) {

            if ($second >= 60)
            {
                $was_changed = false;
                break;
            }
            try {
                $current_value=$this->getText(Yii::app()->params['test_mappings']['flags'][$num_flag]);
                if ($current_value == $ver_value)
                {
                    $was_changed=true;
                    break;
                }
            } catch (Exception $e) {}
            sleep(1);
        }
        return $was_changed;
    }

    /**
     * mail_comes - это метод для проверки, что необходимое письмо пришло
     * mail_theme - тема письма, которое мы ожидаем
     * возвращаем true, если пришло
     * возвращаем false, если не пришло
     */
    public function mail_comes ($mail_theme)
    {
        $is_here=false;
        $a = "xpath=//*[@id='mlTitle']/tbody/tr[";
        $b = "]/td[2]";
        $count = 1;
        while (true)
        {
            $result = "";
            $result .= $a;
            $result .= (string)$count;
            $result .= $b;
            if ($this->isVisible($result))
            {
                $this->mouseOver($result);
                if (($this->getText($result))==$mail_theme)
                {
                    $is_here = true;
                    break;
                }
                else
                {
                    $count++;
                }
            }
            else
            {
                break;
            }
        }
        return $is_here;
    }
}

