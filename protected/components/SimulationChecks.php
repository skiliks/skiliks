<?php

/**
 * Класс для возврата значений
 * с метода UserService::getSimulationContentsAndConfigs(...)
 */
class SimulationChecks {

    /**
     * Результат выполнения метода
     * @var bool
     */
    public $return = false;
    /**
     * Страница на которую будет редирект
     * @var null
     */
    public $redirect = null;
    /**
     * Массив конфигов для старта симуляции
     * @var array
     */
    public $data = [];

    /**
     * Устанавлевает url для ридеректа
     * @param $redirect
     * @return SimulationChecks $this
     */
    public function setRedirect($redirect) {
        $this->redirect = $redirect;
        return $this;
    }

    /**
     * Данные что нужно вернуть
     * @param array $data
     * @return SimulationChecks $this
     */
    public function setData(array $data) {
        $this->data = $data;
        $this->return = true;
        return $this;
    }
}