<?php
/**
 * Class FakeActivityAction
 * Нужно для вывода Leg_action detail not_sent and incorrect_sent
 */
class FakeActivityAction implements IGameAction{

    /**
     * @var array список легэкшинов которых нет в бд
     */
    public $leg_actions = [
        'A_incorrect_sent'=>'incorrect_sent',
        'A_not_sent'=>'not_sent'
    ];
    /**
     * @var string нужно для возврата значения
     */
    public $leg_action = '';

    /**
     * @param Activity $activity
     */
    public function __construct(Activity $activity){

        if(isset($this->leg_actions[$activity->code])){
            $this->leg_action = $this->leg_actions[$activity->code];
        }else{
            $this->leg_action = 'not found';
        }
    }

    /**
     * @return string возвращает код лэгэкшина
     */
    public function getCode(){
        return $this->leg_action;
    }

}