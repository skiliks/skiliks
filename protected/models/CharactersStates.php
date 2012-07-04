<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CharactersStates
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class CharactersStates extends CActiveRecord{
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return 'characters_states';
    }
}

?>
