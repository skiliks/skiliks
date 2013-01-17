<?php

class m130117_151238_fk_conflict_fix extends CDbMigration
{
	public function up()
	{
        $this->delete('mail_box');
        $this->dropForeignKey('fk_mail_box_subject_id', 'mail_box');
        $this->addForeignKey(
            'fk_mail_box_subject_id',
            'mail_box',
            'subject_id',
            'mail_character_themes',
            'id',
            'CASCADE',
            'CASCADE'
        );
	}

	public function down()
	{

        $this->dropForeignKey('fk_mail_box_subject_id', 'mail_box');
        $this->addForeignKey(
            'fk_mail_box_subject_id',
            'mail_box',
            'subject_id',
            'mail_character_themes',
            'id',
            'CASCADE',
            'CASCADE'
        );
	}

}