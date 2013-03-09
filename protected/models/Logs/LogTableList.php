<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 09.03.13
 * Time: 13:28
 * To change this template use File | Settings | File Templates.
 */

class LogTableList {

    /**
     * @param $simulation Simulation
     */
    public function __construct($simulation) {
        $this->simulation = $simulation;
    }

    /**
     * @return LogTable[]
     */
    private function getTables() {
        $simulation = $this->simulation;
        return [
            new WindowLogTable($simulation->log_windows),
            new ActivityLogTable($simulation->log_activity_actions),
            new MailLogTable($simulation->log_mail),
            new DialogLogTable($simulation->log_dialogs),
            new AssessmentResultTable($simulation->assessment_points),
            new AssessmentDetailTable(
                array_merge(
                    $simulation->simulation_mail_points,
                    $simulation->assessment_dialog_points,
                    LogHelper::getMailPointsDetail(LogHelper::RETURN_DATA,['sim_id' => $simulation->primaryKey])['data']
                )
            )
        ];
    }

    public function asArray()
    {
        return $this->getTables();
    }

    public function asExcel() {
        $xls = new PHPExcel();
        $xls->removeSheetByIndex(0);
        foreach ($this->getTables() as $table) {
            $worksheet = new PHPExcel_Worksheet($xls, $table->getTitle());
            foreach ($table->getHeaders() as $i => $title) {
                $worksheet->setCellValueByColumnAndRow($i, 1, $title);
            }
            foreach ($table->getData() as $i => $row) {
                foreach ($row as $j => $value) {
                    $worksheet->setCellValueByColumnAndRow($j, $i + 2, $value, true);

                }
            }
            foreach ($table->getHeaders() as $i => $title) {
                $worksheet->getColumnDimensionByColumn($i)->setAutoSize(true);
            }
            $xls->addSheet($worksheet);
        }
        return new PHPExcel_Writer_Excel2007($xls);
    }
}