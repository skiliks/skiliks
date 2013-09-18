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

    public $language = ["management"  => "Управленческие навыки",
        "performance" => "Результативность",
        "time"        => "Эффективность использования времени",
        "overall"     => "Итоговый рейтинг",
    ];

    public function getHeaders()
    {
        return [
            'Тип оценки',
            'Оценка'
        ];
    }

    public function getId() {
        return 'overall-rate';
    }

    public function getTitle()
    {
        return 'Итоговый рейтинг';
    }

    /**
     * @param \AssessmentOverall $rate
     * @return array
     */
    protected function getRow($rate)
    {
        return [
            $this->getCategoryCodeName($rate),
            $rate->value . '%'
        ];
    }

    public function getRowId($row)
    {
        return sprintf(
            'overall-rate-%s ',
            $row[0]
        );
    }
}