<?php

/**
 * Class CountRegisteredUsers
 */
class СountRegisteredUsers {

    /**
     * @var
     */
    private $condition;
    /**
     * @var DateTime
     */
    public $fromDate;
    /**
     * @var DateTime
     */
    public $toDate;
    /**
     * @var
     */
    private $dbCommand;

    /**
     * @var array
     */
    public  $totalRegistrations   = [];
    /**
     * @var array
     */
    public  $totalPersonals       = [];
    /**
     * @var array
     */
    public  $totalCorporate       = [];
    /**
     * @var array
     */
    public  $totalNonActivePersonals = [];
    /**
     * @var array
     */
    public  $totalNonActiveCorporate = [];


    /**
     * Задает промежуток времени от текущого +1 день
     */
    public function __construct() {
        $this->fromDate = new DateTime();
        $date = new DateTime();
        $this->toDate   = $date->add(new DateInterval('P1D'));
    }

    /**
     * Групирует пользователей по дате
     */
    public function getAllUserForDays() {

        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y-%m-%d\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y-%m-%d\')');
        $this->dbCommand->limit(30);
        $rows = $this->getData();
        $this->saveAllUsers($rows);
    }

    /**
     * Групирует пользователей по меяцу
     */
    public function getAllUserForMonths() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%M\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%M\')');
        $this->dbCommand->limit(12);
        $rows = $this->getData();
        $this->saveAllUsers($rows);
    }

    /**
     * Групирует не активных пользователей по дате
     */
    public function getNonActiveUsersForDays() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y-%m-%d\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y-%m-%d\')');
        $this->addCondition("user.status = 0");
        $this->dbCommand->where($this->condition);
        $this->dbCommand->limit(30);
        $rows = $this->getData();
        $this->saveActiveUsers($rows);
    }

    /**
     * Групирует не активных пользователей по месяцу
     */
    public function getNonActiveUsersForMonths() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%M\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%M\')');
        $this->addCondition("user.status = 0");
        $this->dbCommand->where($this->condition);
        $this->dbCommand->limit(12);
        $rows = $this->getData();
        $this->saveActiveUsers($rows);
    }

    /**
     * Групирует всех пользователей по году
     */
    public function getAllUserForYears() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y\')');
        $this->dbCommand->limit(1);
        $rows = $this->getData();
        $this->saveAllUsers($rows);
    }

    /**
     * Групирует не активных пользователей
     */
    public function getNonActiveUserForYears() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y\')');
        $this->addCondition("user.status = 0");
        $this->dbCommand->where($this->condition);
        $this->dbCommand->limit(1);
        $rows = $this->getData();
        $this->saveActiveUsers($rows);
    }


    /**
     * Сортирует данные для вывода в обратнои порядке
     */
    public function prepare_for_view() {
        $this->totalRegistrations   = array_reverse($this->totalRegistrations);
        $this->totalPersonals       = array_reverse($this->totalPersonals);
        $this->totalCorporate       = array_reverse($this->totalCorporate);
        $this->totalNonActivePersonals = array_reverse($this->totalNonActivePersonals);
        $this->totalNonActiveCorporate = array_reverse($this->totalNonActiveCorporate);
    }

    /**
     * Наполняет массив дней количеством всех пользователей
     * @param array $rows массив колечеств пользователей
     */
    private function saveAllUsers(array $rows) {
        foreach($rows as $row) {
            $this->totalRegistrations[$row['date']] = $row['total_users'];
            $this->totalPersonals[$row['date']]     = $row['personal_users'];
            $this->totalCorporate[$row['date']]     = $row['corporate_users'];
        }
    }

    /**
     * Наполняет массив дней количеством активных пользователей
     * @param $rows
     */
    private function saveActiveUsers($rows) {
        foreach($rows as $row) {
            $this->totalActiveRegistrations[$row['date']] = $row['total_users'];
            $this->totalNonActivePersonals[$row['date']]     = $row['personal_users'];
            $this->totalNonActiveCorporate[$row['date']]     = $row['corporate_users'];
        }
    }

    /**
     * Подсчитевает количество всех, персональных, копоративных пользователей
     * @param string $select часть sql запроса
     */
    private function _prepare_dbCommand($select = "") {
        $this->dbCommand = Yii::app()->db->createCommand();
        $this->condition = "";
        $this->dbCommand->select($select . '
                                  count(user.id) as total_users,
                                  count(corporate.user_id) as corporate_users,
                                  count(personal.user_id) as personal_users');
        $this->dbCommand->from('user');
        $this->dbCommand->leftJoin("user_account_corporate corporate", "corporate.user_id = user.id");
        $this->dbCommand->leftJoin("user_account_personal personal",   "personal.user_id  = user.id");
        $this->dbCommand->order("date DESC");
    }

    /**
     * возвращает массив с данныи из БД
     * @return array
     */
    private function getData() {
        return $this->dbCommand->queryAll();
    }

    /**
     * Добавляет часть sql звпроса
     * @param string $condition часть sql выражения
     */
    private function addCondition($condition) {
        if($this->condition != null) {
            $this->condition .= " AND ";
        }
        $this->condition .= $condition;
    }

}