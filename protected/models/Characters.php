<?php


/**
 * Модель персонажей. Хранит информацию о персонажах.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Characters extends CActiveRecord
{
    /**
     * @var integer
     */
    public $id;
    
    /**
     * @var string
     */
    public $title;
    
    /**
     * @var string
     */
    public $fio;
    
    /**
     * @var string
     */
    public $email;
    
    /**
     * @var integer
     */
    public $code;
    
    /**
     * @var string
     */
    public $skype;
    
    /**
     * @var string
     */
    public $phone;
    
    /**
     * @var string
     */
    public $import_id;
    
    const HERO_ID = 1;
    
    /* ----------------------------- */
    
    /**
     * List of Contacts fot main hero phone
     * @return mixed array
     */
    public function getContactsList()
    {
        $characters = self::model()->findAll('code != 1');
        
        $list = [];
        foreach($characters as $character) {
            $list[] = [
                'id'    => $character->id,
                'code'    => $character->code,
                'name'  => $character->fio,
                'title' => $character->title,
                'phone' => $character->phone
            ];
        }
        
        return $list;
    }

    /* ----------------------------- */

    public function isMainHero()
    {
        return 1 === (int)$this->id;
    }


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
            return 'characters';
    }
    
    /**
     * Ограничивает выборку па заданному набору персонажей
     * @param array $ids набор персонажей
     * @return Characters 
     */
    public function byIds($ids)
    {
        $list = implode(',', $ids);
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id in ({$list})"
        ));
        return $this;
    }
    
    /**
     * Ограничивает выборку по коду персонажа
     * @param string $code
     * @return Characters 
     */
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
    
    /**
     * Выборка по конкретному коду персонажа.
     * @param int $id идентификатор персонажа
     * @return Characters 
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
}


