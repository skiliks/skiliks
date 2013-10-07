<?php

class m131002_065528_add_assessment_results_render_type extends CDbMigration
{
	public function up()
	{
        $this->addColumn("profile", "assessment_results_render_type", "VARCHAR (30) DEFAULT 'percentil'");
	}

	public function down()
	{
        $this->dropColumn("profile", "assessment_results_render_type");
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