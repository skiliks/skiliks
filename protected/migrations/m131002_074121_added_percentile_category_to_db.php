<?php

class m131002_074121_added_percentile_category_to_db extends CDbMigration
{
	public function up()
	{
        $this->insert("assessment_category", ["code"=>"percentile"]);
	}

	public function down()
	{
        $this->delete("assessment_category", "code = 'percentile'");
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