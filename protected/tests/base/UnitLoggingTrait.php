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

    private function appendDialog(&$logs, $code, $resultExcelId, $time = 60, $windowType = 24)
    {
        $replica = Replica::model()->getFirstReplica($code);
        $resultReplica = Replica::model()->findByAttributes(['excel_id' => $resultExcelId]);
        $logs[] = [20, $windowType, 'activated', $this->time, 'window_uid' => $this->windowUid, ['dialogId' => $replica->primaryKey, 'lastDialogId' => $resultReplica->primaryKey]];
        $this->time += $time;
        $logs[] = [20, $windowType, 'deactivated', $this->time, 'window_uid' => $this->windowUid, ['dialogId' => $replica->primaryKey, 'lastDialogId' => $resultReplica->primaryKey]];
        $this->windowUid ++;
    }
    private function appendPhoneCall(&$logs, $code, $resultExcelId, $time = 60)
    {
        $this->appendDialog($logs, $code, $resultExcelId, $time, 24);
    }
    private function appendPhoneTalk(&$logs, $code, $resultExcelId, $time = 60)
    {
        $this->appendDialog($logs, $code, $resultExcelId, $time, 23);
    }

    /**
     * @param $logs
     * @param MyDocument $document
     * @param int $time
     */
    private function appendDocument(&$logs, MyDocument $document, $time = 60)
    {
        $logs[] = [40, 42, 'activated', $this->time, 'window_uid' => $this->windowUid, ['fileId' => $document->primaryKey]];
        $logs[] = [40, 42, 'deactivated', $this->time + $time, 'window_uid' => $this->windowUid, ['fileId' => $document->primaryKey]];
        $this->time += $time;
        $this->windowUid ++;
    }

    private function appendSleep(&$logs, $time)
    {
        $logs[] = [1, 1, 'activated', $this->time, 'window_uid' => $this->windowUid];
        $logs[] = [1, 1, 'deactivated', $this->time + $time, 'window_uid' => $this->windowUid];
        $this->time += $time;
        $this->windowUid ++;
    }

    private function appendNewMessage(&$logs, $message, $time = 60, $windowUid = null) {
        if ($windowUid === null) {
            $windowUid = $this->windowUid;
            $this->windowUid ++;
        }
        $logs[] = [10, 13, 'activated', $this->time, 'window_uid' => $windowUid];
        $logs[] = [10, 13, 'deactivated', $this->time + $time, 'window_uid' => $windowUid, ['mailId' => $message->primaryKey]];
        $this->time += $time;

    }

    private function appendMessage(&$logs, $message, $time = 60) {
        $logs[] = [10, 13, 'activated', $this->time, 'window_uid' => $this->windowUid, ['mailId' => $message->primaryKey]];
        $logs[] = [10, 13, 'deactivated', $this->time + $time, 'window_uid' => $this->windowUid, ['mailId' => $message->primaryKey]];
        $this->time += $time;
        $this->windowUid ++;
    }

    private function appendViewMessage(&$logs, $message, $time = 60) {
        $logs[] = [10, 11, 'activated', $this->time, 'window_uid' => $this->windowUid, ['mailId' => $message->primaryKey]];
        $logs[] = [10, 11, 'deactivated', $this->time + $time, 'window_uid' => $this->windowUid, ['mailId' => $message->primaryKey]];
        $this->time += $time;
        $this->windowUid ++;

    }

    /**
     * @param $logs
     * @param int $window
     * @param int $time
     * @param null $windowUid
     */
    private function appendWindow(&$logs, $window, $time = 60, $windowUid = null) {
        if ($windowUid === null) {
            $windowUid = $this->windowUid;
            $this->windowUid ++;
        }
        $logs[] = [round($window, -1), $window, 'activated', $this->time, 'window_uid' => $windowUid];
        $logs[] = [round($window, -1), $window, 'deactivated', $this->time + $time, 'window_uid' => $windowUid];
        $this->time += $time;
        $this->windowUid ++;

    }

}