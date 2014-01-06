<?php

class m131227_090242_clean_old_themes extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_mail_phrases_character_theme_id', 'mail_phrases');
        $this->dropForeignKey('communication_themes_characher', 'communication_themes');
        $this->dropForeignKey('communication_themes_scenario', 'communication_themes');
        $this->dropForeignKey('mail_template_subject_id', 'mail_template');
        $this->dropForeignKey('fk_mail_box_subject_id', 'mail_box');

        $this->dropColumn('mail_template', 'subject_id');
        $this->dropColumn('mail_box', 'subject_id');
        $this->dropColumn('mail_phrases', 'character_theme_id');

        $this->dropTable('log_communication_theme_usage');
        $this->dropTable('flag_communication_theme_dependence');
        $this->dropTable('communication_themes');

	}

	public function down()
	{
        return true;
	}

}