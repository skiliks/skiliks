<?php

class m131225_095557_update_phone_calls extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('phone_calls_theme_id', 'phone_calls');
        $this->update('phone_calls', [
            'theme_id' => null
        ]);
        $this->addForeignKey('fk_phone_calls_theme_id', 'phone_calls', 'theme_id',
            'theme', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		echo "m131225_095557_update_phone_calls migration down.\n";
	}

}