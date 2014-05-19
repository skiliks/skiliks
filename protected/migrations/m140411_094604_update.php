<?php

class m140411_094604_update extends CDbMigration
{
	public function up()
	{
        ini_set('memory_limit', '-1');
        $scenario = 'full';
        $import = new ImportGameDataService($scenario);
        $import->importAll();

        //TODO: поправить логику
        // update file name (version) only after import done {
        $scenario = Scenario::model()->findByAttributes(['slug' => $scenario]);
        if (null !== $scenario) {
            $scenario->filename = basename($import->getFilename());
            $scenario->save(false);
        }
        // update file name (version) only after import done }

        echo "\n'Import complete. \n";
        /* @var $mails Mailbox[] */
        $mails = $data = Yii::app()->getDb()->
            createCommand()
            ->select('id, theme_text')
            ->from('mail_box')
            ->queryAll();
        foreach($mails as $mail) {

            $text = $mail['theme_text'] !== 'Презентация для ГД_драфт версия'?$mail['theme_text']:'Презентация для ГД_рабочая версия';
            $theme = Yii::app()->getDb()->
                createCommand()
                ->select('id')
                ->from('theme')
                ->where("text = '{$text}'")
                ->queryRow();
            if(!empty($theme)) {
                $this->update('mail_box', [
                    'theme_id' => $theme['id']
                ], "id = {$mail['id']}");

            } else {
                echo $mail['theme_text']." - не найдено \r\n";
            }
        }

        $this->dropColumn('mail_box', 'theme_text');
	}

	public function down()
	{

	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}