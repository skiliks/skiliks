<?php


/**
 * Модель персонажей. Хранит информацию о персонажах.
 *
 * @param integer $id
 * @param string  $title
 * @param string  $fio
 * @param string  $email
 * @param integer $code
 * @param string  $skype
 * @param string  $phone
 * @param string  $import_id
 * @param string  $has_mail_theme
 * @param string  $has_phone_theme
 * @param string  $sex, 'M' - male, 'F' - female, '-' - undefined
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Character extends CActiveRecord
{
    const SEX_MALE   = 'M';
    const SEX_FEMALE = 'F';

    const HERO_ID = 1;

    /* -------------------------------------------------------------------------------------- */

    /**
     * List of Contacts fot main hero phone
     * @return mixed array
     */
    public function getContactsList()
    {
        $characters = self::model()->findAll('code != 1');
        
        $list = [];
        foreach($characters as $character) {
            $list[] = $character->getClientAttributes();
        }
        
        return $list;
    }

    /**
     * @internal param $character
     * @return array
     */
    public function getClientAttributes()
    {
        return [
            'id'    => $this->id,
            'code'  => $this->code,
            'name'  => $this->fio,
            'title' => $this->title,
            'phone' => $this->phone
        ];
    }

    public function isMainHero()
    {
        return 1 === (int)$this->id;
    }

    /**
     * @return bool
     */
    public function isFemale() {
        return self::SEX_FEMALE === $this->sex;
    }

    /**
     * @return bool
     */
    public function isMale() {
        return self::SEX_MALE === $this->sex;
    }

    /* ---------------------------------------------------------------------- */

    /**
     *
     * @param type $className
     * @return Character
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
     * @return Character
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
     * Выборка по конкретному коду персонажа.
     * @param int $id идентификатор персонажа
     * @return Character
     */
    public function byId($id)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "id = {$id}"
        ));
        return $this;
    }
}


