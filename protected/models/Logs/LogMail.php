<?php
/**
 * @property integer  $id
 * @property integer  $sim_id
 * @property integer  $mail_id
 * @property integer  $window
 * @property integer  $mail_task_id
 * @property datetime $start_time
 * @property datetime $end_time
 * @property string   $full_coincidence  , '-' or mail_template.code
 * @property string   $part1_coincidence , '-' or mail_template.code
 * @property string   $part2_coincidence , '-' or mail_template.code
 * @property string   $window_uid        , md5, windows unique ID - currently used to determine several mail new windows
 * @property bool     $is_coincidence
 *
 * @property Simulation $simulation
 * @property Window     $window_obj
 * @property MailBox    $mail
 */
class LogMail extends CActiveRecord
{
    public function __toString()
    {
        return sprintf("%s %s %s", $this->start_time, $this->end_time, $this->full_coincidence ?: '—');
    }

    /**
     * @return string
     */
    public function dump() {
        return $this . "\n";
    }

    protected function afterSave()
    {
        /** @var $template MailTemplate|null */
        if ($this->full_coincidence !== null && $this->full_coincidence !== '-') {
            $template = $this->simulation->game_type->getMailTemplate(['code' => $this->full_coincidence]);
        } else {
            $template = (null !== $this->mail) ? $this->mail->template : null;
        };

        if ($template !== null){
            // If mail is correct MS
            $existsAssessmentPoint = AssessmentPoint::model()->findByAttributes([
                'sim_id' => $this->sim_id,
                'mail_id' => $template->id
            ]);

            if (empty($existsAssessmentPoint)) {
                $mailPoints = $this->simulation->game_type->getMailPoints(['mail_id' => $template->id]);
                /** @var MailPoint[] $mailPoints */
                foreach ($mailPoints as $mailPoint) {
                    $assessmentPoint = new AssessmentPoint();
                    $assessmentPoint->sim_id = $this->sim_id;
                    $assessmentPoint->point_id = $mailPoint->point_id;
                    $assessmentPoint->mail_id = $template->id;
                    $assessmentPoint->value = $mailPoint->add_value;

                    $assessmentPoint->save();
                }
            }
        }

        parent::afterSave();
    }

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param string $className
     * @return LogMail
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'log_mail';
    }

    /**
     * @return array
     */
    public function relations()
    {
        return array(
            'mail'       => array(self::BELONGS_TO, 'MailBox', 'mail_id'),
            'simulation' => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
            'window_obj' => array(self::BELONGS_TO, 'Window', 'window'),
        );
    }
}
