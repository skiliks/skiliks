<?php

class m130725_195720_add_simulation_results_popup_cache extends CDbMigration
{
	public function up()
	{
        $this->addColumn('simulations', 'results_popup_partials_path', 'TEXT');
        $this->addColumn('simulations', 'results_popup_cache', 'BLOB');
	}

	public function down()
	{
        $this->dropColumn('simulations', 'results_popup_partials_path');
        $this->dropColumn('simulations', 'results_popup_cache');
	}
}