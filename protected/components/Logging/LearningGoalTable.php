<?php
namespace application\components\Logging;

    /**
     * \addtogroup Logging
     * @{
     */
/**
 * Детально расписанные поведения по матрицам диалогов и писем. Присутствует гребаный ад из LogHelper-a
 */
class LearningGoalTable extends LogTable
{
    public function getId()
    {
        return 'learning-goal';
    }

    public function getTitle()
    {
        return 'Learning Goal';
    }

    public function getHeaders()
    {
        return [
            'Код',
            'Наименование цели обучения',
            'Набранные положительные очки',
            'Оценка (0-100%)',
            'Уровень проблем (0-100%)',
            'Код области обучения',
        ];
    }

    /**
     * @param \SimulationLearningGoal $row
     * @return array
     * @throws \Exception
     */
    protected function getRow($row)
    {
        $value = round($row->value, 2);
        $value = (string)$value === '0' ? '0.00':$value;

        return [
            $row->learningGoal->code,
            $row->learningGoal->title,
            $row->value,
            $row->percent,
            $row->problem,
            $row->learningGoal->learningArea->code
        ];
    }


    public function getRowId($row)
    {
        return sprintf(
            'learning-goal-code-%s ',
            $row[0]
        );
    }
}
/**
 * @}
 */