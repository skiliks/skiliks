<?php

class UniversalLog extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * simulations.id
     * @var int
     */
    public $sim_id;

    /**
     * See LogHelper window codes
     * @var integer
     */
    public $window_id;

    /**
     * mail_box.id
     * @var int
     */
    public $mail_id;

    /**
     * my_documents.id
     * @var int
     */
    public $file_id;

    /**
     * dialogs.id
     * @var int
     */
    public $dialog_id;

    /**
     * dialogs.id
     * @var int
     */
    public $last_dialog_id;

    /**
     * dialogs.id
     * @var int
     */
    public $activity_action_id;

    /**
     * '00:00::00' current game day
     * @var string
     */
    public $start_time;
    
    /**
     * '00:00::00' current game day
     * @var string
     */
    public $end_time;        
    
    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     *
     * @param type $className
     * @return Characters
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
        return 'universal_log';
    }
}
