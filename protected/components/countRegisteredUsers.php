<?php
/**
 * Created by JetBrains PhpStorm.
 * User: macbookpro
 * Date: 14.10.13
 * Time: 12:37
 * To change this template use File | Settings | File Templates.
 */

class countRegisteredUsers {

    private $condition;
    public $fromDate;
    public $toDate;
    private $dbCommand;

    public  $totalRegistrations   = [];
    public  $totalPersonals       = [];
    public  $totalCorporate       = [];
    public  $totalNonActivePersonals = [];
    public  $totalNonActiveCorporate = [];


    public function __construct() {
        $this->fromDate = new DateTime();
        $date = new DateTime();
        $this->toDate   = $date->add(new DateInterval('P1D'));
    }

    public function getAllUserForDays() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y-%m-%d\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y-%m-%d\')');
        $this->dbCommand->limit(30);
        $rows = $this->getData();
        $this->saveAllUsers($rows);
    }

    public function getAllUserForMonths() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%M\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%M\')');
        $this->dbCommand->limit(12);
        $rows = $this->getData();
        $this->saveAllUsers($rows);
    }

    public function getNonActiveUsersForDays() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y-%m-%d\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y-%m-%d\')');
        $this->addCondition("user.status = 0");
        $this->dbCommand->where($this->condition);
        $this->dbCommand->limit(30);
        $rows = $this->getData();
        $this->saveActiveUsers($rows);
    }

    public function getNonActiveUsersForMonths() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%M\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%M\')');
        $this->addCondition("user.status = 0");
        $this->dbCommand->where($this->condition);
        $this->dbCommand->limit(12);
        $rows = $this->getData();
        $this->saveActiveUsers($rows);
    }

    public function getAllUserForYears() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y\')');
        $this->dbCommand->limit(12);
        $rows = $this->getData();
        $this->saveAllUsers($rows);
    }

    public function getNonActiveUserForYears() {
        $this->_prepare_dbCommand('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y\') as date,');
        $this->dbCommand->group('DATE_FORMAT(FROM_UNIXTIME(createtime), \'%Y\')');
        $this->addCondition("user.status = 0");
        $this->dbCommand->where($this->condition);
        $this->dbCommand->limit(1);
        $rows = $this->getData();
        $this->saveActiveUsers($rows);
    }


    public function prepare_for_view() {
        $this->totalRegistrations   = array_reverse($this->totalRegistrations);
        $this->totalPersonals       = array_reverse($this->totalPersonals);
        $this->totalCorporate       = array_reverse($this->totalCorporate);
        $this->totalNonActivePersonals = array_reverse($this->totalNonActivePersonals);
        $this->totalNonActiveCorporate = array_reverse($this->totalNonActiveCorporate);
    }

    private function saveAllUsers($rows) {
        foreach($rows as $row) {
            $this->totalRegistrations[$row['date']] = $row['total_users'];
            $this->totalPersonals[$row['date']]     = $row['personal_users'];
            $this->totalCorporate[$row['date']]     = $row['corporate_users'];
        }
    }

    private function saveActiveUsers($rows) {
        foreach($rows as $row) {
            $this->totalActiveRegistrations[$row['date']] = $row['total_users'];
            $this->totalNonActivePersonals[$row['date']]     = $row['personal_users'];
            $this->totalNonActiveCorporate[$row['date']]     = $row['corporate_users'];
        }
    }

    private function addOneDayConditionsToQuery() {
        $this->addFromDateCondition();
        $this->addToDateCondition();
    }



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
    }

    private function getData() {
        return $this->dbCommand->queryAll();
    }

    private function addFromDateCondition() {
        $this->addCondition('FROM_UNIXTIME(user.createtime) >= (\''.date_format($this->fromDate, 'Y-m-d').'\')');
    }

    private function addToDateCondition() {
        $this->addCondition('FROM_UNIXTIME(user.createtime) <= (\''.date_format($this->toDate, 'Y-m-d').'\')');
    }

    private function addCondition($condition) {
        if($this->condition != null) {
            $this->condition .= " AND ";
        }
        $this->condition .= $condition;
    }

}