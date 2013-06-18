<?php

class m130618_104328_log_theme extends CDbMigration
{
	public function up()
	{
        $this->createTable('log_communication_theme_usage', [
            'id'=>'pk',
            'sim_id' => 'int(11) NOT NULL',
            'communication_theme_id' => 'int(11) NOT NULL',
        ]);
        $this->addForeignKey('fk_log_communication_theme_usage_sim_id', 'log_communication_theme_usage', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_log_communication_theme_usage_communication_theme_id', 'log_communication_theme_usage', 'communication_theme_id', 'communication_themes', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropTable('log_communication_theme_usage');
        $this->dropForeignKey('fk_log_communication_theme_usage_sim_id', 'log_communication_theme_usage');
        $this->dropForeignKey('fk_log_communication_theme_usage_communication_theme_id', 'log_communication_theme_usage');
	}

}