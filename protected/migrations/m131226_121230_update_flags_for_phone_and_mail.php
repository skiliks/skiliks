<?php

class m131226_121230_update_flags_for_phone_and_mail extends CDbMigration
{
	public function up()
	{
        $this->createTable('flag_outbox_mail_theme_dependence',[
            'id' => 'pk',
            'outbox_mail_theme_id' => 'int(11) NOT NULL',
            'flag_code' => 'varchar(10) NOT NULL',
            'scenario_id' => 'int(11) NOT NULL',
            'import_id' => 'varchar(60) NOT NULL',
            'value' => 'int(1) NOT NULL'
        ]);

        $this->addForeignKey('fk_flag_outbox_mail_theme_dependence_outbox_mail_theme_id', 'flag_outbox_mail_theme_dependence', 'outbox_mail_theme_id',
            'outbox_mail_themes', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_flag_outbox_mail_theme_dependence_flag_code', 'flag_outbox_mail_theme_dependence', 'flag_code',
            'flag', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_flag_outbox_mail_theme_dependence_scenario_id', 'flag_outbox_mail_theme_dependence', 'scenario_id',
            'scenario', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('flag_outgoing_phone_theme_dependence',[
            'id' => 'pk',
            'outgoing_phone_theme_id' => 'int(11) NOT NULL',
            'flag_code' => 'varchar(10) NOT NULL',
            'scenario_id' => 'int(11) NOT NULL',
            'import_id' => 'varchar(60) NOT NULL',
            'value' => 'int(1) NOT NULL'
        ]);

        $this->addForeignKey('fk_flag_outgoing_phone_theme_dependence_outgoing_phone_theme_id', 'flag_outgoing_phone_theme_dependence', 'outgoing_phone_theme_id',
            'outgoing_phone_themes', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_flag_outgoing_phone_theme_dependence_flag_code', 'flag_outgoing_phone_theme_dependence', 'flag_code',
            'flag', 'code', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_flag_outgoing_phone_theme_dependence_scenario_id', 'flag_outgoing_phone_theme_dependence', 'scenario_id',
            'scenario', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_flag_outbox_mail_theme_dependence_outbox_mail_theme_id', 'flag_outbox_mail_theme_dependence');
        $this->dropForeignKey('fk_flag_outbox_mail_theme_dependence_flag_code', 'flag_outbox_mail_theme_dependence');
        $this->dropForeignKey('fk_flag_outbox_mail_theme_dependence_scenario_id', 'flag_outbox_mail_theme_dependence');

        $this->dropTable('flag_outbox_mail_theme_dependence');


        $this->dropForeignKey('fk_flag_outgoing_phone_theme_dependence_outgoing_phone_theme_id', 'flag_outgoing_phone_theme_dependence');
        $this->dropForeignKey('fk_flag_outgoing_phone_theme_dependence_flag_code', 'flag_outgoing_phone_theme_dependence');
        $this->dropForeignKey('fk_flag_outgoing_phone_theme_dependence_scenario_id', 'flag_outgoing_phone_theme_dependence');

        $this->dropTable('flag_outgoing_phone_theme_dependence');

	}

}