<?php
namespace application\components\Logging;

    /**
     * \addtogroup Logging
     * @{
     */
/**
 * Детально расписанные поведения по матрицам диалогов и писем. Присутствует гребаный ад из LogHelper-a
 */
class AssessmentPointsTable extends LogTable
{
    public function getId()
    {
        return 'assessment-points';
    }

    public function getTitle()
    {
        return 'Assessment - points';
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
            'Вызвавшая реплика (id_записи)',
            'Вызвавшая реплика (Код события)',
            'Вызвавшая реплика (номер шага)',
            'Вызвавшая реплика (номер реплики)',
            'Вызвавшее исходящее письмо '
        ];
        //return ['Point Code', 'Point Description', 'Type Scale', 'Scale', 'Value', 'Replica ID', 'Dialog Code', 'Replica Step', 'Replica Number', 'Outbox mail'];
    }

    /**
     * @param \AssessmentPoint $row
     * @return array
     * @throws \Exception
     */
    protected function getRow($row)
    {
        $result = [
            $row->point->learning_goal->code,
            $row->point->learning_goal->title,
            $row->point->code,
            $row->point->title,
            $row->point->type_scale,
            $row->point->scale,
            $row->value
        ];

        if ($row->dialog_id) {
            $result[] = $row->replica->excel_id;
            $result[] = $row->replica->code;
            $result[] = $row->replica->step_number;
            $result[] = $row->replica->replica_number;
            $result[] = '-';
        } elseif ($row->mail_id) {
            $result = array_pad($result, 11, '-');
            $result[] = $row->mail->code;
        } elseif ($row->task_id) {
            throw new \Exception('Not implemented yet');
        } else {
            $result = array_pad($result, 12, '-');
        }

        return $result;
    }


    public function getRowId($row)
    {
        return sprintf(
            'assessment-detail-point-%s assessment-detail-learning-goal-%s ',
            $row[2],
            $row[0]
        );
    }
}
/**
 * @}
 */