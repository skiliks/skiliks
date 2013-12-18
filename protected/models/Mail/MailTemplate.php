<?php
/**
 * Шаблон набора писем. Копируется в рамках симуляции в почтовый ящик польщзователя.
 *
 * @property integer $id
 * @property integer $group_id, mail_group.id
 * @property integer $sender_id, characters.id
 * @property integer $receiver_id, characters.id
 * @property integer $subject_id, communication_theme.id
 * @property integer $type // ???
 * @property string  $type_of_importance: none', 'spam', '2_min', 'plan', 'info', 'first_category', ...
 * @property string  $sent_at, datetime
 * @property string  $message
 * @property string  $code, 'M1', 'MS8' ...
 * @property string  $flag_to_switch, 'F12'
 * @property string  $import_id
 * @property string  $theme_id
 * @property string  $theme_prefix
 *
 * @property CommunicationTheme       $subject_obj
 * @property ActivityParent[]         $termination_parent_actions
 * @property MailAttachmentTemplate[] $attachments
 * @property Scenario                 $game_type
 * @property Theme                    $theme
 *
 */
class MailTemplate extends CActiveRecord implements IGameAction
{
    /**
     * Implements interface
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return bool
     */
    public function isMS(){
        return substr($this->code, 0, 2) === 'MS';
    }

    /**
     * Returns parent template
     * @return MailTemplate
     */
    public function getParent()
    {
        $subject = $this->subject_obj;
        if (! $subject->mail_prefix) {
            return null;
        }
        $newPrefix = preg_replace('/^(re|fwd)/', '', $subject->mail_prefix) ? : null;
        $parentTheme = CommunicationTheme::model()->findByAttributes(['code' => $subject->code, 'mail_prefix' => $newPrefix]);

        return $this->game_type->getMailTemplate(['subject_id' => $parentTheme->primaryKey]);
    }

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param string $className
     * @return MailTemplate
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

    public function relations()
    {
        return [
            'termination_parent_actions' => [self::HAS_MANY, 'ActivityParent', 'mail_id'],
            'subject_obj'                => [self::BELONGS_TO, 'CommunicationTheme', 'subject_id'],
            'attachments'                => [self::HAS_MANY, 'MailAttachmentTemplate', 'mail_id'],
            'game_type'                  => [self::BELONGS_TO, 'Scenario', 'scenario_id'],
            'sender'                     => [self::BELONGS_TO, 'Character', 'sender_id'],
            'recipient'                  => [self::BELONGS_TO, 'Character', 'receiver_id'],
            'theme'                      => [self::BELONGS_TO, 'Theme', 'theme_id']
        ];
    }
}


