<?php
/*
 * Нужно для вывода Leg_action detail not_sent and incorrect_sent
 */
class FakeActivityAction implements IGameAction{

    public $leg_actions = [
        'A_incorrect_sent'=>'incorrect_sent',
        'A_not_sent'=>'not_sent'
    ];
    public $leg_action = '';
    public function __construct(Activity $activity){
        if(isset($this->leg_actions[$activity->code])){
            $this->leg_action = $this->leg_actions[$activity->code];
        }else{
            $this->leg_action = 'not found';
        }
    }
    public function getCode(){
        return $this->leg_action;
    }

}