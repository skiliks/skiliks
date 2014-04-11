<?php

class m131218_154340_add_theme_text_mailbox extends CDbMigration
{
	public function up()
	{
        ini_set('memory_limit', '-1');

        $this->addColumn('mail_box', 'theme_text', 'varchar(255) default null');

        $mails = $data = Yii::app()->getDb()->
            createCommand()
            ->select('id, subject_id')
            ->from('mail_box')
            ->queryAll();
        foreach($mails as $mail) {
            $data = Yii::app()->getDb()->
                createCommand()
                ->select('text, mail_prefix')
                ->from('communication_themes')
                ->where("id = {$mail['subject_id']}")
                ->queryRow();
            $this->update('mail_box', [
                'theme_text' => $data['text'],
                'mail_prefix' => $data['mail_prefix']
            ], "id = {$mail['id']}");
        }

	}

	public function down()
	{

	}

}