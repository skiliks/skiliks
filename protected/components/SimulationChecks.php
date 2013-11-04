<?php

class SimulationChecks {

    public $return = false;
    public $redirect = null;
    public $data = [];

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