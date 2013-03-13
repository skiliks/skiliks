<?php
namespace application\components\Logging;

    /**
     * \addtogroup Logging
     * @{
     */
/**
 * Детально расписанные поведения. Присутствует гребаный ад из LogHelper-a
 */
class AssessmentDetailTable extends LogTable
{
    public function getId()
    {
        return 'assessment-detail';
    }

    public function getTitle()
    {
        return 'Assessment - detail';
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
     * @param \AssessmentDetail $row
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
            $row->getAddValue()
        ];

        if ($row->dialog_id) {
            $result[] = $row->replica->excel_id;
            $result[] = $row->replica->code;
            $result[] = $row->replica->step_number;
            $result[] = $row->replica->replica_number;
        } elseif ($row->mail_id) {
            $result = array_pad($result, 11, '-');
            $result[] = $row->mail->code;
        } elseif ($row->task_id) {
            throw new \Exception('Not implemented yet');
        } else {
            $result = array_pad($result, 11, '-');

            if ($row->point->learning_goal_code == 331 or $row->point->learning_goal_code == 332) {
                $result[] = '3. Оценка Mail Inbox';
            } else if ($row->point->learning_goal_code == 333) {
                $result[] = '4. Оценка Mail Outbox';
            } else {
                $result[] = '-';
            }
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