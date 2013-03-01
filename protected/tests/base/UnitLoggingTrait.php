<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 01.03.13
 * Time: 19:50
 * To change this template use File | Settings | File Templates.
 */

trait UnitLoggingTrait {
    private $time = 32400;
    private $windowUid = 1;

    private function appendDialog(&$logs, $code, $resultExcelId, $time = 60)
    {
        $replica = Replica::model()->getFirstReplica($code);
        $resultReplica = Replica::model()->findByAttributes(['excel_id' => $resultExcelId]);
        $logs[] = [20, 24, 'activated', $this->time, 'window_uid' => $this->windowUid, ['dialogId' => $replica->primaryKey, 'lastDialogId' => $resultReplica->primaryKey]];
        $this->time += $time;
        $logs[] = [20, 24, 'deactivated', $this->time, 'window_uid' => $this->windowUid, ['dialogId' => $replica->primaryKey, 'lastDialogId' => $resultReplica->primaryKey]];
    }
    private function appendSleep(&$logs, $time)
    {
        $logs[] = [1, 1, 'activated', $this->time, 'window_uid' => $this->windowUid];
        $logs[] = [1, 1, 'deactivated', $this->time + $time, 'window_uid' => $this->windowUid];
        $this->time += $time;
    }

    private function appendNewMessage(&$logs, $message, $time = 60) {
        $logs[] = [10, 13, 'activated', $this->time, 'window_uid' => $this->windowUid];
        $logs[] = [10, 13, 'deactivated', $this->time + $time, 'window_uid' => $this->windowUid, ['mailId' => $message->primaryKey]];
        $this->time += $time;

    }

    private function appendMessage(&$logs, $message, $time = 60) {
        $logs[] = [10, 13, 'activated', $this->time, 'window_uid' => $this->windowUid, ['mailId' => $message->primaryKey]];
        $logs[] = [10, 13, 'deactivated', $this->time + $time, 'window_uid' => $this->windowUid, ['mailId' => $message->primaryKey]];
        $this->time += $time;

    }

    private function appendViewMessage(&$logs, $message, $time = 60) {
        $logs[] = [10, 11, 'activated', $this->time, 'window_uid' => $this->windowUid, ['mailId' => $message->primaryKey]];
        $logs[] = [10, 11, 'deactivated', $this->time + $time, 'window_uid' => $this->windowUid, ['mailId' => $message->primaryKey]];
        $this->time += $time;

    }

    private function appendWindow(&$logs, $window, $time = 60) {
        $logs[] = [round($window, -1), $window, 'activated', $this->time, 'window_uid' => $this->windowUid];
        $logs[] = [round($window, -1), $window, 'deactivated', $this->time + $time, 'window_uid' => $this->windowUid];
        $this->time += $time;

    }

}