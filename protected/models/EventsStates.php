<?php



/**
 * Вроде как устаревший функционал.
 * 
 * 
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class EventsStates extends CActiveRecord
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
     * event_samples.id
     * @var integer
     */
    public $event_id;
    
    /**
     * dialogss.id
     * @var integer
     */
    public $cur_dialog_id;  
    
    /**
     * event_samples.id
     * @var integer
     */
    public $result;    
    
    /** ------------------------------------------------------------------------------------------------------------ **/
    
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'events_states';
    }
    
    public function bySimulation($simId)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => 'sim_id = '.(int)$simId,
            'order' => 'id desc',
            'limit' => 1
        ));
        return $this;
    }
}


