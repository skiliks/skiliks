<?php

class m140303_103112_price extends CDbMigration
{
	public function up()
	{
        $this->createTable('price', [
            'id' => 'pk',
            'name'=>'varchar(50) default null',
            'alias'=>'varchar(50) default null',
            'from'=>'int(11) default null',
            'to'=>'int(11) default null',
            'in_RUB' => 'decimal(10,2) default null',
            'in_USD' => 'decimal(10,2) default null',
        ]);
	}

	public function down()
	{
		$this->dropTable('price');
	}

}