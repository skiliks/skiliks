<?php



/**
 * Шаблон набора писем. Копируется в рамках симуляции в почтовый ящик польщзователя.
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class MailTemplateModel extends CActiveRecord{
    
    /**
     *
     * @param type $className
     * @return MailTemplateModel
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
            return 'mail_template';
    }
    
    public function byCode($code)
    {
        $this->getDbCriteria()->mergeWith(array(
            'condition' => "code = '{$code}'"
        ));
        return $this;
    }
}

?>
