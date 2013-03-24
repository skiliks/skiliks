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
     * (начиная с открытия окна браузера до самого входа в dev-режим).
     * Пример использования - тест F1_SK1403_Test.php , строка 21
     */
    public function start_simulation()
    {
        $this->deleteAllVisibleCookies();
        $this->windowMaximize();
        $this->open('/ru');
        $this->optimal_click("xpath=//header/nav/a[4]");
        $this->waitForVisible("css=.login>input");
        $this->type("css=.login>input", "tatiana@skiliks.com");
        $this->type("css=.password>input", "123123");
        $this->optimal_click("css=.submit>input");

        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("xpath=(//*[contains(text(),'Cheats')])")) break;
            } catch (Exception $e) {}
            sleep(1);
        }
        /*
        $this->optimal_click("xpath=//header/nav/a[4]");

        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("xpath=(//*[contains(text(),'Начать симуляцию в режиме developer')])")) break;
            } catch (Exception $e) {}
            sleep(1);
        }

        $this->optimal_click("xpath=(//*[contains(text(),'Начать симуляцию в режиме developer')])");
        */

        $this->open('/simulation/developer/1'); // для full simulation

        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible("id=addTriggerSelect")) break;
            } catch (Exception $e) {}
            sleep(1);
        }
        // hren'
        sleep(10);
    }

    /**
     * run_event - это метод для запуска события по его event_code.
     * Пример использования - тест F1_SK1403_Test.php , строка 23
     */
    // next_event - это локатор следующего события(звонок телефона или приход письма), которого мы ожидаем и должны что-то с ним сделать после
    // after - если надо что-то с этим локатором сделать после, то сюда пишем click, а если нет - то каку-ю херню можно написать. Оно расспознает тпока только click
    // запустили event = ET1.1 -> next_event = css=li.icon-active.phone a (звонок телефона) -> after = click (мы кликаем по иконке телефона)
    // если еще что-то надо, то можно дописать в switch
    public function run_event($event, $next_event, $after)
    {
        $this->type(Yii::app()->params['test_mappings']['dev']['event_input'], "$event");
        $this->optimal_click(Yii::app()->params['test_mappings']['dev']['event_create']);

        for ($second = 0; ; $second++) {
            if ($second >= 60) $this->fail("timeout");
            try {
                if ($this->isVisible($next_event))
                {
                    // switch чтобы была возможность расширить дополнительными действиями (кроме клика), а default - если никакие действия не нужны
                    switch ($after) {
                        case 'click':
                            {
                                $this->click($next_event);
                                break;
                            }
                        default:
                            break;
                    }
                    break;
                }
            } catch (Exception $e) {}
            sleep(1);
        }
    }

    /**
     * call_phone - это метод для звонка по телефону, когда телефон не активен (иконка не движется).
     * Где whom - это адресат письма, а theme - тема звонка.
     * Пример использования - тест F3_SK1338-SK1341_Test.php , строка 59
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
     * reply_call - это метод для ответа на входящий звонок, когда телефон активен (иконка движется).
     * Пример использования - ...
     */
    public function reply_call ()
    {
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['reply']);
    }

    /**
     * no_reply_call - это метод для игнора входящего звонка, когда телефон активен (иконка движется).
     * Пример использования - ...
     */
    public function no_reply_call ()
    {
        $this->optimal_click("css=li.icon-active.phone a");
        $this->optimal_click(Yii::app()->params['test_mappings']['phone']['no_reply']);
    }

    /**
     * write_mail_active - это метод для создания письма, когда мейл-клиент активен (иконка мигает).
     * Пример использования - тест F3_SK1338-SK1341_Test.php , строка 144-147
     */
    public function write_mail_active()
    {
        $this->optimal_click("css=li.icon-active.mail a");
        $this->optimal_click(Yii::app()->params['test_mappings']['mail']['to_whom']);
    }

    /**
     * optimal_click - это метод для корректного нажатия на элемент (ожидание элемента и только потом нажатие).
     * Пример использования - тест F3_SK1338-SK1341_Test.php , строка 36
     */
    public function optimal_click ($loc)
    {
        $this->waitForVisible($loc);
        $this->click($loc);
    }

    /**
     * how_much_time - это метод для определения поточного игрового времени.
     * Метод возвращает массив, где первый элемент - это поточное количество часов, а второй элемент -
     * поточное количество минут.
     * Пример использования - метод transfer_time (см. ниже)
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
     * которые должны происходить с задержкой, где differ -это колличество минут задежки.
     * Пример использования - тест F14_SK1427_P_Test.php , строка 147
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
     * что телефон не звонит на протяжении 1 реальной минуты).
     * locator - локатор элемента, наличие которого мы проверяем.
     * Возвращаем true, если произошло событие и
     * возвращаем false, если не произошло.
     * Пример использования - тест F4_SK1413_N_Test.php , строка 49-52
     * Пример использования - тест F4_SK1413_P_Test.php , строка 48-60
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
     * и соответсвует значению ver_value.
     * Возвращаем true, если поменялось значение флага и
     * возвращаем false, если не изменилось.
     * Пример использования - тест F3_SK1338-SK1341_Test.php , строка 54
     */
    public function verify_flag ($num_flag, $ver_value)
    {
        //sleep(5);
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
     * mail_comes - это метод для проверки, что необходимое письмо пришло.
     * mail_theme - тема письма, которое мы ожидаем.
     * Возвращаем true, если пришло письмо с необходимой темой и
     * возвращаем false, если не пришло.
     * Пример использования - тест F14_SK1427_P_Test.php , строка 51-55
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

    /**
     * incoming_counter - это метод для проверки, что количество писем = .
     * count - количество писем, которые мы ожидаем увидеть во "входящих" на момент указанного времени (время устанавливаем перед вызовом этого метода).
     * Возвращаем true, если количество ожидаемых писем и реальных входящих совпадают
     * возвращаем false, если нет
     * Пример использования - тест Case_SK1471_Test.php , строка 33-37
     */
    public function incoming_counter ($count)
    {
        $same_number = false;
        $was_changed = false;
        $this->waitForVisible(Yii::app()->params['test_mappings']['icons']['mail']);
        $this->waitForVisible("css=li.icon-active.mail a");
        if ($this->isVisible("css=li.icon-active.mail a"))
        {
            for ($second = 0; ; $second++) {
                if ($second >= 60)
                {
                    $was_changed = false;
                    break;
                }
                try {
                    if ($this->isVisible(Yii::app()->params['test_mappings']['icons']['mail']))
                    {
                        $was_changed = true;
                        break;
                    }
                } catch (Exception $e) {}
                sleep(1);
            }
        }
        else
        {
            $was_changed = true;
        }

        sleep(20);
        $numb_of_incoming = 0;
        if ($was_changed==true)
        {
             $numb_of_incoming = (int)($this->getText("//*[@id='icons_email']/span"));
            if ($numb_of_incoming == $count)
            {
                $same_number=true;
            }
        }

        return $same_number;
    }

    /**
     *
     */
    public function mail_open ($mail_theme)
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
                    $this->optimal_click($result);
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

