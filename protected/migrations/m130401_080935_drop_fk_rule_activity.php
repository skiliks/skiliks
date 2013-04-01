<?php

class m130401_080935_drop_fk_rule_activity extends CDbMigration
{
	public function up()
	{
        //$this->dropForeignKey('fk_performance_rule_activity_id', 'performance_rule');
	}

	public function down()
	{
        echo "Удалить его нужно";
        return false;
	}

}