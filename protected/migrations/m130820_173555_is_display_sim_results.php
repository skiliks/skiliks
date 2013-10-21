<?php

class m130820_173555_is_display_sim_results extends CDbMigration
{
	public function up()
	{
        $model = new Invite();
        if( false == $model->hasAttribute('VARNAME') ) {
            $this->addColumn('invites', 'is_display_simulation_results', 'TINYINT(1) DEFAULT 1');
        }
	}

	public function down()
	{
        $this->dropColumn('invites', 'is_display_simulation_results');
	}
}