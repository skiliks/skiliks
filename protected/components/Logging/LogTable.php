<?php

namespace application\components\Logging;
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
     * @param array $logs List of log entities. Different for each subclass
     */
    public function __construct($logs)
    {
        $this->logs = $logs;
    }

    /**
     * Override to returns list of headers
     *
     * @return string[]
     * @abstract
     */
    abstract public function getHeaders();

    /**
     * Accepts single log row, returns plain array with data to be displayed
     *
     * @param $row
     * @return mixed
     */
    abstract protected function getRow($row);

    /**
     * Override to return sheet title for XLS export and HTML header title
     * @return string
     */
    abstract public function getTitle();

    /**
     * Override to return unique ID (used on HTML page)
     * @return string
     */
    abstract protected function getId();

    /**
     * Returns array of arrays of plain values
     * @return array[]
     */
    final public function getData() {
        $result = [];
        foreach ($this->logs as $log) {
            $result[] = $this->getRow($log);
        }
        return $result;
    }
}