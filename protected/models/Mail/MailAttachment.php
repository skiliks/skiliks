<?php
/**
 * Вложения писем в рамках конкретной симуляции.
 *
 * @property integer id
 * @property integer $mail_id, mail_box.id
 * @property integer $file_id
 * @property MyDocument $myDocument
 */
class MailAttachment extends CActiveRecord
{
    /* ------------------------------------------------------------------------------------------------------------ */
    
    /**
     * @param string $className
     * @return MailAttachment
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
        return 'mail_attachments';
    }

    /**
     * Выбрать по заданному набору писем
     * @param array $mailIds
     * @return MailAttachment
     */
    public function byMailIds($mailIds)
    {
        $ids = implode(',', $mailIds);
        
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "mail_id in ({$ids})"
        ));
        return $this;
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'myDocument' => array(self::BELONGS_TO, 'MyDocument', 'file_id'),
        );
    }
}


