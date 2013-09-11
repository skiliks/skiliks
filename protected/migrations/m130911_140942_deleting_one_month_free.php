<?php

class m130911_140942_deleting_one_month_free extends CDbMigration
{
	public function up()
	{
        $this->execute("UPDATE `tariff` SET `benefits` = 'Free updates' WHERE `id` = '1'");
	}

	public function down()
	{
        $this->execute("UPDATE `tariff` SET `benefits` = 'Free updates, 1 month free' WHERE `id` = '1'");
		return false;
	}
}