<?php

class m130306_153033_remove_old_user_schema extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('fk_user_groups_gid', 'user_groups');
        $this->dropForeignKey('fk_user_groups_uid', 'user_groups');

        $this->dropForeignKey('fk_users_activation_code_uid', 'users_activation_code');

        $this->dropForeignKey('fk_simulations_user_id', 'simulations');

        $this->dropTable('groups');

        $this->dropTable('user_groups');
        $this->dropTable('users_activation_code');

        $this->dropForeignKey('fk_day_plan_log_uid', 'day_plan_log');

        $this->dropTable('users');
	}

	public function down()
	{
		echo "m130306_153033_remove_old_user_schema does not support migration down.\n";
	}
}