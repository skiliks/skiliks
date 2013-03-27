<?php

class m130327_104550_add_max_negative_value extends CDbMigration
{
	public function up()
	{
        $this->addColumn('learning_goal', 'max_negative_value', 'DECIMAL(5,2)');
	}

	public function down()
	{
        $this->dropColumn('learning_goal', 'max_negative_value');
	}

}