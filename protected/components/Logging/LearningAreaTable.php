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
        $result = [
            $row->learningArea->code,
            $row->learningArea->title,
            $row->value
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