<?php
namespace application\components\Logging;

/**
 * Class LogTableList
 * @package application\components\Logging
 */
class LogTableList
{

    /**
     * @param $simulation \Simulation
     */
    public function __construct($simulation)
    {
        $this->simulation = $simulation;
    }

    /**
     * @return LogTable[]
     */
    private function getTables()
    {
        $simulation = $this->simulation;
        return [
            new WindowLogTable($simulation->log_windows),
            new AssessmentDetailTable(
                $simulation->getAssessmentPointDetails()
            ),
            new AssessmentResultTable($simulation->assessment_points),
            # TODO plan
            new MailLogTable($simulation->log_mail),
            new DocumentLogTable($simulation->log_documents),
            new DialogLogTable($simulation->log_dialogs),
            new ActivityLogTable($simulation->log_activity_actions),

        ];
    }

    public function asArray()
    {
        return $this->getTables();
    }

    public function asExcel()
    {
        $xls = new \PHPExcel();
        $xls->removeSheetByIndex(0);
        foreach ($this->getTables() as $table) {
            $worksheet = new \PHPExcel_Worksheet($xls, $table->getTitle());
            $xls->addSheet($worksheet);
            foreach ($table->getHeaders() as $i => $title) {
                $worksheet->setCellValueByColumnAndRow($i, 1, $title);
            }
            foreach ($table->getData() as $i => $row) {
                foreach ($row as $j => $value) {
                    $worksheet->setCellValueByColumnAndRow($j, $i + 2, $value, true);
                    $worksheet->getStyleByColumnAndRow($j, $i + 2)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                }
            }

            $worksheet->getStyle('A1:Z1')->applyFromArray(['font' => ['bold' => true]]);
            foreach ($table->getHeaders() as $i => $title) {
                $worksheet->getColumnDimensionByColumn($i)->setAutoSize(true);
            }

        }
        return new \PHPExcel_Writer_Excel2007($xls);
    }
}
