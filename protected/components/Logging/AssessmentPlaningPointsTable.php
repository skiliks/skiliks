<?php
namespace application\components\Logging;

    /**
     * \addtogroup Logging
     * @{
     */
/**
 * Детально расписанные поведения по матрицам диалогов и писем. Присутствует гребаный ад из LogHelper-a
 */
class AssessmentPlaningPointsTable extends LogTable
{
    public function getId()
    {
        return 'assessment-planing-points';
    }

    public function getTitle()
    {
        return 'Assessment planing - points';
    }

    public function getHeaders()
    {
        return [
            'Номер цели обучения',
            'Наименование цели обучения',
            'Номер поведения',
            'Наименование поведения',
            'Тип поведения',
            'Вес поведения',
            'Проявление',
            'Вызвавшая задача(код)',
         ];
        //return ['Point Code', 'Point Description', 'Type Scale', 'Scale', 'Value', 'Task ID'];
    }

    /**
     * @param \AssessmentPoint $row
     * @return array
     * @throws \Exception
     */
    protected function getRow($row)
    {
        $result = [
            $row->heroBehaviour->learning_goal->code,
            $row->heroBehaviour->learning_goal->title,
            $row->heroBehaviour->code,
            $row->heroBehaviour->title,
            \HeroBehaviour::getTypeScaleName($row->heroBehaviour->type_scale),
            $row->heroBehaviour->scale,
            $row->value,
            $row->task->code,
        ];

        return $result;
    }


    public function getRowId($row)
    {
        return sprintf(
            'assessment-planing-point-%s assessment-planing-point-learning-goal-%s ',
            $row[2],
            $row[0]
        );
    }
}
/**
 * @}
 */