<?php

class m131126_150446_create_tariff_plan_id_on_invite extends CDbMigration
{
	public function up()
	{
        $this->addColumn('invites', 'tariff_plan_id', 'int(11) default null');

        $this->addForeignKey('fk_invites_tariff_plan_id', 'invites', 'tariff_plan_id',
            'tariff_plan', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropColumn('invites', 'tariff_plan_id');

        $this->dropForeignKey('fk_invites_tariff_plan_id', 'invites');

    }
}