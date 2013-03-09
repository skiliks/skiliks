<?php


namespace application\components\Logging;


/**
 * \addtogroup Logging
 * @{
 */
/**
 * Абстрактный класс, от которого наследуются все таблицы. Логики в нем нет, просто не дает выстрелить себе в ногу
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
     * Returns list of table headers
     *
     * @return string[]
     * @abstract
     */
    abstract public function getHeaders();

    /**
     * Accepts single log row, returns plain array with data to be displayed
     *
     * @param $row
     * @return array
     */
    abstract protected function getRow($row);

    /**
     * Returns sheet title for XLS export and HTML header title
     * @return string
     */
    abstract public function getTitle();

    /**
     * Returns unique ID (used on HTML page)
     * @return string
     */
    abstract public  function getId();

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
/**
 * @}
 */