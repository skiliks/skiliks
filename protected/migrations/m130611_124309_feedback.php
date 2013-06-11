<?php

class m130611_124309_feedback extends CDbMigration
{
	public function up()
	{
        $this->addColumn('feedback', 'addition', 'DATETIME DEFAULT NULL');
	}

	public function down()
	{
        $this->dropColumn('feedback', 'addition');
	}
}