<?php

class m130404_120924_remove_max_negative_value extends CDbMigration
{
	public function up()
	{
        $this->dropColumn('learning_goal', 'max_negative_value');
	}

	public function down()
	{
        $this->addColumn('learning_goal', 'max_negative_value', 'DECIMAL(5,2)');
	}
}