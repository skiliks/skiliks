<?php

class m121214_123125_mail_static_text extends CDbMigration
{
    public function up()
    {
        $this->update('mail_character_themes', array('letter_number' => null), "letter_number= ''");
        $this->alterColumn('mail_template', 'code', 'varchar(5) NOT NULL');
        $this->createIndex('mail_code_unique', 'mail_template', 'code', true);
        $this->addForeignKey(
            'fk_mail_character_themes_letter_number',
            'mail_character_themes', 'letter_number',
            'mail_template', 'code');
	}

    public function down()
    {
        $this->dropForeignKey(
            'fk_mail_character_themes_letter_number',
            'mail_character_themes');
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