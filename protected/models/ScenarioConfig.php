<?php

/**
 * This is the model class for table "scenario_config".
 *
 * The followings are the available columns in table 'scenario_config':
 * @property integer $scenario_id
 * @property string $import_id
 * @property string $game_start_timestamp
 * @property string $game_end_workday_timestamp
 * @property string $game_end_timestamp
 * @property string $game_help_folder_name
 * @property string $game_help_background_jst
 * @property string $game_help_pages
 * @property string $inbox_folder_icons
 * @property string $draft_folder_icons
 * @property string $outbox_folder_icon
 * @property string $trash_folder_icons
 * @property string $read_email_screen_icons
 * @property string $write_new_email_screen_icons
 * @property string $edit_draft_email_screen_icons
 * @property string $game_date
 * @property string $intro_video_path
 * @property string $docs_to_save
 * @property string $is_calculate_assessment
 * @property string $is_display_assessment_result_po_up
 *
 * The followings are the available model relations:
 * @property Scenario $scenario
 */
class ScenarioConfig extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ScenarioConfig the static model class
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
		return 'scenario_config';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('scenario_id, game_start_timestamp, game_end_workday_timestamp, game_end_timestamp, game_help_folder_name, game_help_background_jst, game_help_pages, inbox_folder_icons, draft_folder_icons, outbox_folder_icon, trash_folder_icons, read_email_screen_icons, write_new_email_screen_icons, edit_draft_email_screen_icons, game_date, intro_video_path, docs_to_save, is_calculate_assessment, is_display_assessment_result_po_up', 'required'),
			array('scenario_id', 'numerical', 'integerOnly'=>true),
			array('import_id', 'length', 'max'=>14),
			array('game_start_timestamp, game_end_workday_timestamp, game_end_timestamp, game_help_folder_name, game_help_background_jst, game_date, intro_video_path, docs_to_save, is_calculate_assessment, is_display_assessment_result_po_up', 'length', 'max'=>250),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('scenario_id, import_id, game_start_timestamp, game_end_workday_timestamp, game_end_timestamp, game_help_folder_name, game_help_background_jst, game_help_pages, inbox_folder_icons, draft_folder_icons, outbox_folder_icon, trash_folder_icons, read_email_screen_icons, write_new_email_screen_icons, edit_draft_email_screen_icons, game_date, intro_video_path, docs_to_save, is_calculate_assessment, is_display_assessment_result_po_up', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'scenario' => array(self::BELONGS_TO, 'Scenario', 'scenario_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'scenario_id' => 'Scenario',
			'import_id' => 'Import',
			'game_start_timestamp' => 'Game Start Timestamp',
			'game_end_workday_timestamp' => 'Game End Workday Timestamp',
			'game_end_timestamp' => 'Game End Timestamp',
			'game_help_folder_name' => 'Game Help Folder Name',
			'game_help_background_jst' => 'Game Help Background Jst',
			'game_help_pages' => 'Game Help Pages',
			'inbox_folder_icons' => 'Inbox Folder Icons',
			'draft_folder_icons' => 'Draft Folder Icons',
			'outbox_folder_icon' => 'Outbox Folder Icon',
			'trash_folder_icons' => 'Trash Folder Icons',
			'read_email_screen_icons' => 'Read Email Screen Icons',
			'write_new_email_screen_icons' => 'Write New Email Screen Icons',
			'edit_draft_email_screen_icons' => 'Edit Draft Email Screen Icons',
			'game_date' => 'Game Date',
			'intro_video_path' => 'Intro Video Path',
			'docs_to_save' => 'Docs To Save',
			'is_calculate_assessment' => 'Is Calculate Assessment',
			'is_display_assessment_result_po_up' => 'Is Display Assessment Result Po Up',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('scenario_id',$this->scenario_id);
		$criteria->compare('import_id',$this->import_id,true);
		$criteria->compare('game_start_timestamp',$this->game_start_timestamp,true);
		$criteria->compare('game_end_workday_timestamp',$this->game_end_workday_timestamp,true);
		$criteria->compare('game_end_timestamp',$this->game_end_timestamp,true);
		$criteria->compare('game_help_folder_name',$this->game_help_folder_name,true);
		$criteria->compare('game_help_background_jst',$this->game_help_background_jst,true);
		$criteria->compare('game_help_pages',$this->game_help_pages,true);
		$criteria->compare('inbox_folder_icons',$this->inbox_folder_icons,true);
		$criteria->compare('draft_folder_icons',$this->draft_folder_icons,true);
		$criteria->compare('outbox_folder_icon',$this->outbox_folder_icon,true);
		$criteria->compare('trash_folder_icons',$this->trash_folder_icons,true);
		$criteria->compare('read_email_screen_icons',$this->read_email_screen_icons,true);
		$criteria->compare('write_new_email_screen_icons',$this->write_new_email_screen_icons,true);
		$criteria->compare('edit_draft_email_screen_icons',$this->edit_draft_email_screen_icons,true);
		$criteria->compare('game_date',$this->game_date,true);
		$criteria->compare('intro_video_path',$this->intro_video_path,true);
		$criteria->compare('docs_to_save',$this->docs_to_save,true);
		$criteria->compare('is_calculate_assessment',$this->is_calculate_assessment,true);
		$criteria->compare('is_display_assessment_result_po_up',$this->is_display_assessment_result_po_up,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}