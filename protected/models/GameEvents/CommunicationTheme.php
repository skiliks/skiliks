<?php

/**
 * Содержит соотношения - какому персонажу какой набор тем писем
 * соответствует
 *
 * @property integer $id
 * @property integer $character_id
 * @property string  $constructor_number, "R1", "TXT" ...
 * @property string  $import_id
 * @property string  $wr, right, wrong : "R", "W"
 * @property string  $text
 * @property string  $letter_number, "code" - "M2", "MS45" ...
 * @property string  $phone, "R1", "TXT" ...
 * @property string  $phone_wr, right, wrong : "R", "W"
 * @property string  $phone_dialog_number, ??
 * @property string  $mail_prefix
 * @property integer $mail, ??
 * @property string  $theme_usage, used to filter MSY themes from new mail themes list
 *
 *  Source:
 * "manual" - user write new letter and send it,
 * "dialog" - new mail window was opened by dialog
 * "inbox"  - user write reply email
 * @property string  $source
 *
 * @property Scenario $game_type
 * @property MailTemplate $letter
 *
 */
class CommunicationTheme extends CActiveRecord
{
    const USAGE_OUTBOX     = 'mail_outbox';
    const USAGE_OUTBOX_OLD = 'mail_outbox_old';
    const USAGE_INBOX      = 'mail_inbox';

    const SLUG_RIGHT = 'R';
    const SLUG_WRONG = 'W';

    /** ------------------------------------------------------------------------------------------------------------ **/

    /**
     * @param string $receivers, '1,2,3'
     * @param $parentSubjectId
     *
     * @return integer || NULL
     * @deprecated
     */
    public static function getCharacterThemeId($receivers, $parentSubjectId)
    {
        $characterThemeId = NULL;
        $receiversArr = explode(',', $receivers);

        if (0 < count($receiversArr) && NULL != $parentSubjectId) {
            $characterTheme = CommunicationTheme::model()->findByAttributes([
                'character_id' => reset($receiversArr),
                'code'         => CommunicationTheme::model()->findByPk($parentSubjectId)->code
            ]);

            if (null !== $characterTheme) {
                $characterThemeId = $characterTheme->id;
            }
        }
        
        return $characterThemeId;
    }

    /**
     * @return string
     */
    public function getFormattedTheme()
    {
        return str_replace(['re', 'fwd'], ['Re: ', 'Fwd: '], $this->mail_prefix) . '' . $this->text;
    }
    
    /**
     * @return string
     */
    public function getPrefixForForward()
    {
        return ($this->mail_prefix !== NULL) ? "fwd".$this->mail_prefix : "fwd";
    }

    /**
     * @return string
     */
    public function getPrefixForReply()
    {
        return ($this->mail_prefix !== NULL) ? "re".$this->mail_prefix : "re";
    }


    /**
     * @return MailTemplate | NULL
     */
    public function getMailTemplate() {
        return $this->game_type->getMailTemplate([
            'code' => $this->letter_number
        ]);
    }

    /**
     * @param $simulation
     * @return bool
     */
    public function isBlockedByFlags($simulation) {

        $flagsDependence = $this->game_type->getFlagCommunicationThemeDependencies(['communication_theme_id'=>$this->id]);
        if(empty($flagsDependence)){
            return false;
        }
        foreach($flagsDependence as $flagDependence) {
            /* @var $flagDependence FlagCommunicationThemeDependence  */
            /* @var $flagSimulation SimulationFlag  */
            $flagSimulation = FlagsService::getFlag($simulation, $flagDependence->flag_code);
            if($flagSimulation->value !== $flagDependence->value) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param LogCommunicationThemeUsage $themes
     * @return bool
     */
    public function themeIsUsed($themes) {
        /* @var $theme LogCommunicationThemeUsage */
        foreach($themes as $key => $theme) {
            if((int)$this->id === (int)$theme->communication_theme_id) {
                unset($themes[$key]);
                return true;
            }
        }
        return false;
    }

    /** ------------------------------------------------------------------------------------------------------------ **/
    
    /**
     *
     * @param type $className
     * @return CommunicationTheme
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
        return 'communication_themes';
    }

    public function relations()
    {
        return array(
            'game_type' => array(self::BELONGS_TO, 'Scenario', 'scenario_id')
        );
    }
}


