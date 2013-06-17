<?php

class m130531_093531_agree extends CDbMigration
{
	public function up()
	{
        $this->addColumn("user", "agree_with_terms", "VARCHAR(3) DEFAULT NULL");
	}

	public function down()
	{
        $this->dropColumn("user", "agree_with_terms");
	}

}