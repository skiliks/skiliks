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

    protected function getRow($row)
    {
        if ($row instanceof \SimulationMailPoint) {
            if ($row->point->learning_goal_code == 331 or $row->point->learning_goal_code == 332) {
                $outboxMail = '3. Оценка Mail Inbox';
            } else if ($row->point->learning_goal_code == 333) {
                $outboxMail = '4. Оценка Mail Outbox';
            } else {
                $outboxMail = '';
            }
            return [
                $row->point->learning_goal->code,
                $row->point->learning_goal->title,
                $row->point->code,
                $row->point->title,
                $row->point->type_scale,
                $row->point->scale,
                $row->value,
                '-',
                '-',
                '-',
                '-',
                $outboxMail
            ];
        } else if ($row instanceof \AssessmentDetail) {
            return [
                $row->point->learning_goal->code,
                $row->point->learning_goal->title,
                $row->point->code,
                $row->point->title,
                $row->point->type_scale,
                $row->point->scale,
                $row->getReplicaPoint()->add_value,
                $row->replica->excel_id,
                $row->replica->code,
                $row->replica->step_number,
                $row->replica->replica_number,
                '-'

            ];
        } else if (is_array($row)) {
            $resultRow = array_slice($row, 1);
            array_splice($resultRow, 7, 0, ['-', '-', '-', '-']);
            return $resultRow;
        }
    }
}
/**
 * @}
 */