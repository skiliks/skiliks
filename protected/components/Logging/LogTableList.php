<?php

namespace application\components\Logging {
    /**
     * \addtogroup Logging Все, что связано с логированием
     * Классы, отвечающие за отображение логирования в XLS и в Админке. Для лобавления нового лога нужно создать класс таблицы
     * и добавить его в список, который находится в методе LogTableList::getTables.
     *
     * Класс таблицы должен наследоваться от LogTable
     * @{
     */
    /**
     * Список таблиц, которые попадают в Excel и админку
     *
     * @code
     *  $logTableList = new LogTableList($simulation);
     *  $this->render('log', [
     *      'simulation' => $simulation,
     *      'log_tables' => $logTableList->asArray()
     *  ]);
     * @endcode
     */
    class LogTableList
    {

        /**
         * @param \Simulation $simulation
         */
        public function __construct($simulation)
        {
            $this->simulation = $simulation;
        }

        /**
         * Ordered list of logging tables
         *
         * @return LogTable[]
         */
        private function getTables()
        {
            $simulation = $this->simulation;
            $mail_inbox_aggregate = \LogHelper::getMailBoxAggregated($simulation);
            return [
                new WindowLogTable($simulation->log_windows),
                new DayPlanLogTable($simulation->log_day_plan),
                new AssessmentPointsTable($simulation->assessment_points),
                new AssessmentPlaningPointsTable($simulation->assessment_planing_points),
                new AssessmentCalculationTable($simulation->assessment_calculation),
                new AssessmentResultTable($simulation->assessment_aggregated),
                new MailInboxAggregateTable($mail_inbox_aggregate),
                new DocumentLogTable($simulation->log_documents),
                new DialogLogTable($simulation->log_dialogs),
                new MeetingLogTable($simulation->log_meetings),
                new ActivityLogTable($simulation->log_activity_actions),
                new ActivityAggregated214dTable($simulation->log_activity_actions_aggregated_214d),
                new ActivityAggregatedTable($simulation->log_activity_actions_aggregated),
                new ExcelTable($simulation->simulation_excel_points),
                new PerformanceTable($simulation->performance_points),
                new PerformanceAggregatedTable($simulation->performance_aggregated),
                new StressTable($simulation->stress_points),
                new OverallRateTable($simulation->assessment_overall),
                new LearningGoalTable($simulation->learning_goal),
                new LearningAreaTable($simulation->learning_area),
                new TimeManagementTable($simulation->time_management_aggregated),
                new LogAssessment214gTable($simulation->logAssessment214g),
                new LearningGoalGroupTable($simulation->learning_goal_group),
                new UniversalLogTable($simulation->universal_log)
            ];
        }

        /**
         * Returns list of tables as for template
         * @return LogTable[]
         */
        public function asArray()
        {
            return $this->getTables();
        }

        /**
         * Returns PHP Excel Writer with all tables. For export to excel
         * @return \PHPExcel_Writer_Excel2007
         */
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
                    $worksheet->getColumnDimensionByColumn($i)->setWidth(12);
                }

            }
            return new \PHPExcel_Writer_Excel2007($xls);
        }
    }
}
