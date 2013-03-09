<?php

/**
 * Class LogTable
 *
 * Is base class for different logging tables (HTML and XLS)
 */
abstract class LogTable {
    protected $logs;

    /**
     * Constructor requires list af log objects
     *
     * @param $logs
     */
    public function __construct($logs)
    {
        $this->logs = $logs;
    }

    /**
     * Returns list of headers
     *
     * @return string[]
     */
    abstract public function getHeaders();

    abstract protected function getRow($row);

    abstract public function getTitle();

    abstract protected function getId();

    final public function getData() {
        $result = [];
        foreach ($this->logs as $log) {
            $result[] = $this->getRow($log);
        }
        return $result;
    }
}