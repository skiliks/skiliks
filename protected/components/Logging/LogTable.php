<?php


namespace application\components\Logging;


/**
 * \addtogroup Logging
 * @{
 */
/**
 * Абстрактный класс, от которого наследуются все таблицы. Логики в нем нет, просто не дает выстрелить себе в ногу
 */
abstract class LogTable
{

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
     * May return game-oriented string (CSS class) log table <tr>.
     * This is code for dialog, heroBehaviour, mail or excel_id for replica
     * It is possible to print several ids to string, like "dialog-ET1 replica-853"
     *
     * Warning! Dialog codes has '.' in code - JS libs interprets it as start of CSS class name.
     * So remove all '.' and '#' from getRowId() returned string.
     *
     * @param $rowEntity - log row object
     *
     * @return string
     */
    abstract public function getRowId($rowEntity);

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
