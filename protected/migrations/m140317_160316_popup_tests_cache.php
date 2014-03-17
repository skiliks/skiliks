<?php

class m140317_160316_popup_tests_cache extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulations', 'popup_tests_cache', 'blob default null');
	}

	public function down()
	{
        $this->dropColumn('simulations', 'popup_tests_cache');
	}

}