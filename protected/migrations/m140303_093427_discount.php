<?php

class m140303_093427_discount extends CDbMigration
{
	public function up()
	{
        $this->addColumn(UserAccountCorporate::model()->tableName(), 'discount', 'float(6,2) default 0');
        $this->addColumn(UserAccountCorporate::model()->tableName(), 'start_discount', 'date default null');
        $this->addColumn(UserAccountCorporate::model()->tableName(), 'end_discount', 'date default null');
	}

	public function down()
	{
		$this->dropColumn(UserAccountCorporate::model()->tableName(), 'discount');
		$this->dropColumn(UserAccountCorporate::model()->tableName(), 'start_discount');
		$this->dropColumn(UserAccountCorporate::model()->tableName(), 'end_discount');
	}

}