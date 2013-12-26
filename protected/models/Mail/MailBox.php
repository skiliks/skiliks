<?php
/**
 * Собственно сам почтовый ящик в рамках конкретной симуляции. Все что сюда
 * попадает то и видит пользователь в своей симуляции.
 *
 * @property integer $id
 * @property integer $group_id, mail_group.id
 * @property integer $sender_id, characters.id
 * @property integer $receiver_id, characters.id
 * @property string  $sent_at, datetime
 * @property string  $message
 * @property integer $sim_id, simulations.id
 * @property integer $message_id, mail_box.id .Если текущее письмо это ответ, то message_id - это id исходного письма.
 * @property integer $template_id, mail_template.id
 * @property string  $letter_type
 * @property string | NULL $coincidence_type, тип совпадения - смотри self::COINCIDENCE_xxx
 * @property string | NULL $coincidence_mail_code, MS1, MS2 etc.
 * @property string  $theme_id
 * @property string  $mail_prefix
 * @property string  $constructor_code
 *
 * @property integer (bool) $readed, is readed
 * @property integer (bool) $plan, is planed
 *
 * Столбец нужен для типа сообщения
 *  1 - Входящие,
 *  2 - Исходящие,
 *  3 - Входящие(доставлен),
 *  4 - Исходящие(доставлен)
 * @property integer $type
 *
 * Code, 'M1', 'MS8' ...
 * MS - mail sended by hero
 * MY - ? mail sended by hero yesterday
 * M - mail received during game
 * MY - mail in inbox when game starts
 * @property string  $code
 *
 * @property MailTemplate       $template
 * @property Simulation         $simulation
 * @property CommunicationTheme $subject_obj
 * @property MailAttachment     $attachment
 * @property MailBox            $parentMail
 * @property MailFolder         $folder
 * @property Theme              $theme
 *
 */
class MailBox extends CActiveRecord
{
    /**
     *
     */
    const COINCIDENCE_FULL   = 'full';
    /**
     *
     */
    const COINCIDENCE_PART_1 = 'part1';
    /**
     *
     */
    const COINCIDENCE_PART_2 = 'part2';

    /**
     *
     */
    const FOLDER_INBOX_ID  = 1;
    /**
     *
     */
    const FOLDER_DRAFTS_ID = 2;
    /**
     *
     */
    const FOLDER_OUTBOX_ID = 3;
    /**
     *
     */
    const FOLDER_TRASH_ID  = 4;
    /**
     *
     */
    const FOLDER_NOT_RECEIVED_EMAILS_ID  = 5;

    /**
     *
     */
    const FOLDER_INBOX_ALIAS  = 'inbox';
    /**
     *
     */
    const FOLDER_DRAFTS_ALIAS = 'drafts';
    /**
     *
     */
    const FOLDER_OUTBOX_ALIAS = 'outbox';
    /**
     *
     */
    const FOLDER_TRASH_ALIAS  = 'trash';
    /**
     *
     */
    const FOLDER_NOT_RECEIVED_ALIAS  = 'not received';

    /**
     *
     */
    const TYPE_FORWARD   = 'forward';
    /**
     *
     */
    const TYPE_REPLY     = 'reply';
    /**
     *
     */
    const TYPE_REPLY_ALL = 'replyAll';

    /**
     * TODO: make THIS private
     * @var array<int>
     */
    public static $folderIdToAlias = array(
        self::FOLDER_INBOX_ID  => self::FOLDER_INBOX_ALIAS,
        self::FOLDER_DRAFTS_ID => self::FOLDER_DRAFTS_ALIAS,
        self::FOLDER_OUTBOX_ID => self::FOLDER_OUTBOX_ALIAS,
        self::FOLDER_TRASH_ID  => self::FOLDER_TRASH_ALIAS,
        self::FOLDER_NOT_RECEIVED_EMAILS_ID => self::FOLDER_NOT_RECEIVED_ALIAS
    );

    /**
     * @var array
     */
    public $mailPrefix = [
        'fwd' => 'Fwd: ',
        'fwdfwd' => 'Fwd: Fwd: ',
        'fwdre' => 'Fwd: Re: ',
        'fwdrefwd' => 'Fwd: Re: Fwd: ',
        'fwdrere' => 'Fwd: Re: Re: ',
        'fwdrerere' => 'Fwd: Re: Re: Re: ',
        're' => 'Re: ',
        'refwd' => 'Re: Fwd: ',
        'rere' => 'Re: Re: ',
        'rerefwd' => 'Re: Re: Fwd: ',
        'rerere' => 'Re: Re: Re: ',
        'rererere' => 'Re:: Re: Re: Re: '
    ];

    /** ------------------------------------------------------------------------------------------------------------ **/

    public function markReplied()
    {
        $this->reply = 1;
    }

    /**
     * is Email has "R" theme
     * @return bool
     */
    public function isRight()
    {
        return CommunicationTheme::SLUG_RIGHT == $this->getWR();
    }

    /**
     * @return bool
     */
    public function isMS()
    {
        return null !== $this->coincidence_mail_code;
    }

    /**
     * @return boolean
     */
    public function isSended() {
        return MailBox::FOLDER_OUTBOX_ID == $this->group_id;
    }

    /**
     * @return boolean
     */
    public function isPlanned() {
        return true === (bool)$this->plan;
    }

    /**
     * @return array of string
     */
    public function getCopyCharacterCodes()
    {
        $copies = MailCopy::model()->findAllByAttributes(['mail_id' => $this->id]);

        $res = [];

        foreach ($copies as $copy) {
            $res[] = $copy->recipient->code;
        }

        return $res;
    }

    /**
     * @return bool
     */
    public function isHasCoincidence()
    {
        return null === $this->coincidence_mail_code;
    }

    /**
     * @param string $type, self::COINCIDENCE_FULL, self::COINCIDENCE_PART_1, self::COINCIDENCE_PART_2
     * @return bool
     */
    public function isHasCoincidenceByType($type)
    {
        if (false === in_array($type, $this->getCoincidenceTypes())) {
            throw new Exception(sprintf('Wrong mail coincidence type %s.', $type));
        }        
        
        return $type === $this->coincidence_type;
    }

    /**
     * @return array of string
     */
    public function getCoincidenceTypes()
    {
        return array(self::COINCIDENCE_FULL, self::COINCIDENCE_PART_1, self::COINCIDENCE_PART_2);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'mail_box';
    }

    /**
     * @return mixed
     */
    public function getGroupName() {
        return self::$folderIdToAlias[$this->group_id];
    }

    /**
     * @return bool
     */
    public function isOutBox(){
        return ($this->group_id === '2' || $this->group_id === '3');
    }

    /**
     * @return bool
     */
    public function isInBox(){
        return ($this->group_id === '1' || $this->group_id === '4');
    }

    /**
     * @return string
     */
    public function getRecipientsCode(){
        $recipients = MailRecipient::model()->findAllByAttributes(['mail_id'=>$this->id]);
        $codes = [];
        /* @var $recipient MailRecipient */
        foreach($recipients as $recipient){
            $codes[] = $recipient->receiver->code;
        }
        return implode(',',$codes);
    }

    /**
     * @return string
     */
    public function getCopiesCode(){
        $copies = MailCopy::model()->findAllByAttributes(['mail_id'=>$this->id]);
        $codes = [];
        /* @var $copy MailCopy */
        foreach($copies as $copy){
            $codes[] = $copy->recipient->code;
        }
        return implode(',',$codes);
    }

    /* ------------------------------------------------------------------------------------------------------------ */

    /**
     *
     * @param string $className
     * @return MailBox 
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }

    /**
     * @return array
     */
    public function relations() {
        return array(
            'subject_obj' => array(self::BELONGS_TO, 'CommunicationTheme', 'subject_id'),
            'template'    => array(self::BELONGS_TO, 'MailTemplate', 'template_id'),
            'simulation'    => array(self::BELONGS_TO, 'Simulation', 'sim_id'),
            'attachment'    => array(self::HAS_ONE, 'MailAttachment', 'mail_id'),
            'parentMail'    => array(self::BELONGS_TO, 'MailBox', 'message_id'),
            'folder'        => array(self::BELONGS_TO, 'MailFolder', 'group_id'),
            'theme'         => [self::BELONGS_TO, 'Theme', 'theme_id']
        );
    }

    /**
     * Возвращает отформатированую тему с очётом префиксов
     * @param string $prefix префикс например re,fwd, rere ...
     * @return string
     */
    public function getFormattedTheme($prefix='') {
        $prefix = $prefix === null?'':$prefix;
        $this->mail_prefix = $this->mail_prefix === null?'':$this->mail_prefix;
        return str_replace(['re', 'fwd'], ['Re: ', 'Fwd: '], $prefix.$this->mail_prefix) . $this->theme->text;
    }

    /**
     * Возвращает текст письма по получител и теме(с префиксом)
     * @return string
     */
    public function getMessageByReceiverAndTheme() {
        return $this->
            simulation->
                game_type->
                    getMailTemplate(
                    [
                        'receiver_id'=>$this->receiver_id,
                        'theme_id'=>$this->theme_id,
                        'mail_prefix'=> $this->mail_prefix
                    ])->message;
    }

    /**
     * Возвращает W/R/N
     * @return string
     */
    public function getWR() {
        $outbox_theme = $this->simulation->game_type->getOutboxMailTheme([
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


