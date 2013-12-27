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
 * @property string  $mail_prefix
 *
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
        if (! $this->mail_prefix) {
            return null;
        }

        return $this->game_type->getMailTemplate(
            ['receiver_id'=>$this->receiver_id, 'mail_prefix' => null, 'theme_id'=>$this->theme_id]
        );
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
            'attachments'                => [self::HAS_MANY, 'MailAttachmentTemplate', 'mail_id'],
            'game_type'                  => [self::BELONGS_TO, 'Scenario', 'scenario_id'],
            'sender'                     => [self::BELONGS_TO, 'Character', 'sender_id'],
            'recipient'                  => [self::BELONGS_TO, 'Character', 'receiver_id'],
            'theme'                      => [self::BELONGS_TO, 'Theme', 'theme_id']
        ];
    }

    /**
     * Возвращает W/R/N
     * @return string
     */
    public function getWR() {
        $outbox_theme = $this->game_type->getOutboxMailTheme([
            'character_to_id' => $this->receiver_id,
            'theme_id' => $this->theme_id,
            'mail_prefix' => $this->mail_prefix
        ]);

        if(null === $outbox_theme){
            return OutboxMailTheme::SLUG_WRONG;
        }
        return $outbox_theme->wr;
    }
}


