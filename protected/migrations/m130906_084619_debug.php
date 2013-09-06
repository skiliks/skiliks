<?php

class m130906_084619_debug extends CDbMigration
{
	public function up()
	{
        $this->execute("CREATE TABLE time_management_aggregated_debug LIKE time_management_aggregated");
	}

	public function down()
	{
        $this->execute("DROP TABLE time_management_aggregated_debug");
	}

}