<?php

class m121219_141725_assesmend_fix_fk extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('assassment_agregated_FK_simulations'          , 'assassment_agregated');
        $this->addForeignKey(
            'assassment_agregated_FK_simulations',
            'assassment_agregated',
            'sim_id',
            'simulations',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

	public function down()
	{
		echo "m121219_141725_assesmend_fix_fk does not support migration down.\n";
		return false;
	}

	/*
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
	}

	public function safeDown()
	{
	}
	*/
}