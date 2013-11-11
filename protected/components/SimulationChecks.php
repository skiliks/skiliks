<?php

/*
 * Класс для возврата значений
 * с метода UserService::getSimulationContentsAndConfigs(...)
 */
class SimulationChecks {

    public $return = false; //Результат выполнения метода
    public $redirect = null; // Страница на которую будет редирект
    public $data = []; //Массив конфигов для старта симуляции

    public function setRedirect($redirect) {
        $this->redirect = $redirect;
        return $this;
    }

    public function setData(array $data) {
        $this->data = $data;
        $this->return = true;
        return $this;
    }
}