<?php
/**
 * Created by JetBrains PhpStorm.
 * User: macbookpro
 * Date: 18.09.13
 * Time: 18:37
 * To change this template use File | Settings | File Templates.
 */

namespace application\components\Logging;


class ManagementSkillsAnalysis2 extends LogTable  {

    public $logs = [];

    public function __construct($learning_goal_group = false, $learning_area = false) {
        foreach($learning_goal_group as $lgg) {

            for($i=0; $i < 2; $i++) {

                $new = new \stdClass();

                // for sorting
                $new->code = $lgg->learningGoalGroup->code;

                foreach($learning_area as $area) {
                    if( substr($lgg->learningGoalGroup->code, 0, 1) == $area->learningArea->code ) {
                        $new->group = $area->learningArea->code . ". " . $area->learningArea->title;
                    }
                }

                $new->title = str_replace("_", ".", $lgg->learningGoalGroup->code) . " " . $lgg->learningGoalGroup->title;

                if($i == 0) {
                    $new->rating_scale = "negative";
                    $new->rating = $lgg->problem;
                }
                else {
                    $new->rating_scale = "positive";
                    if($lgg->learningGoalGroup->code == "1_5") {
                        $new->rating = "Не оценивается";
                    }
                    else {
                        $new->rating = $lgg->percent;
                    }
                }

                if(($lgg->learningGoalGroup->code == "1_5" or $lgg->learningGoalGroup->code == "2_3" or $lgg->learningGoalGroup->code == "3_4") && $i==0) {

                    $this->logs[] = $new;
                    // making the result
                    $new = new \stdClass();
                    foreach($learning_area as $area) {
                        if( substr($lgg->learningGoalGroup->code, 0, 1) == $area->learningArea->code ) {
                            $new->group = $area->learningArea->code . ". " . $area->learningArea->title;
                            // TODO Переделать на последний элемент
                            $new->code = substr($lgg->learningGoalGroup->code, 0, 1) . "_" . "9";
                            $new->rating_scale = "combined";
                            $new->title = "ИТОГО";
                            $value = round($area->value, 2);
                            $value = (string) $value === '0' ? '0.00':$value;
                            $new->rating = $value;
                        }
                    }
                }

                $this->logs[] = $new;
            }
        }

        uasort($this->logs, array($this, 'objectSort'));
    }

    private function objectSort($f1,$f2)
    {
        if($f1->code < $f2->code) return -1;
        elseif($f1->code > $f2->code) return 1;
        else return 0;
    }

    public function getHeaders()
    {
        return [
            'Группа навыков',
            'Навык',
            'Шкала оценки',
            'Навык, оценка (0-100%)'
        ];
    }

    public function getHeaderWidth() {
        return [
            24,
            24,
            14,
            40,
            14
        ];
    }

    public function getId() {
        return 'management-rate';
    }

    public function getTitle()
    {
        return 'Управленческие навыки';
    }

    /**
     * @param \AssessmentOverall $rate
     * @return array
     */
    protected function getRow($rate)
    {
        return [
            $rate->group,
            $rate->title,
            $rate->rating_scale,
            $rate->rating
        ];
    }

    public function getRowId($row)
    {
        return sprintf(
            'overall-rate-%s ',
            $row[0]
        );
    }

    public function getCellValueFormat($columnNo, $rowNo = null) {
        if (1 == $columnNo) {
            return \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00;
        } else {
            return \PHPExcel_Style_NumberFormat::FORMAT_TEXT;
        }
    }
}