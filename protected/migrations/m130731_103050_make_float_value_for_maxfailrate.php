<?php

class m130731_103050_make_float_value_for_maxfailrate extends CDbMigration
{
	public function up()
	{
        $this->alterColumn('max_rate', 'rate', 'FLOAT');
	}

	public function down()
	{
		echo "m130731_103050_make_float_value_for_maxfailrate does not support migration down.\n";
	}
}