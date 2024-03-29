<?php
namespace application\components\Logging;

    /**
     * \addtogroup Logging
     * @{
     */
/**
 * Детально расписанные поведения по матрицам диалогов и писем. Присутствует гребаный ад из LogHelper-a
 */
class LearningAreaTable extends LogTable
{
    public function getId()
    {
        return 'learning-area';
    }

    public function getTitle()
    {
        return 'Learning Area';
    }

    public function getHeaders()
    {
        return [
            'Код',
            'Наименование области обучения',
            'Оценка (0-100%)',
            'Score',
        ];
        //return ['Point Code', 'Point Description', 'Type Scale', 'Scale', 'Value', 'Replica ID', 'Dialog Code', 'Replica Step', 'Replica Number', 'Outbox mail'];
    }

    /**
     * @param \SimulationLearningArea $row
     * @return array
     * @throws \Exception
     */
    protected function getRow($row)
    {
        $value = round($row->value, 2);
        $value = (string)$value === '0' ? '0.00':$value;
        $result = [
            $row->learningArea->code,
            $row->learningArea->title,
            $value,
            round($row->score, 2)
        ];

        return $result;
    }


    public function getRowId($row)
    {
        return sprintf(
            'learning-area-%s learning-area-code-%s ',
            $row[2],
            $row[0]
        );
    }
}
/**
 * @}
 */