<?php

class m121221_164301_activity_add_colums extends CDbMigration
{
	public function up()
	{
        $this->addColumn(
            'activity',
            'numeric_id',
            'INT DEFAULT NULL'
        );
        $this->addColumn(
            'activity',
            'is_keep_last_category',
            'TINYINT(1) DEFAULT 0'
        );
	}

	public function down()
	{
        $this->dropColumn(
            'activity',
            'numeric_id'
        );
        $this->dropColumn(
            'activity',
            'is_keep_last_category'
        );
	}
}