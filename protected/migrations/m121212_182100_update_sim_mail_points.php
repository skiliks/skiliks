<?php

/**
 *
 * @author slavka
 */
class m121212_182100_update_sim_mail_points extends CDbMigration 
{
    public function up()
	{
        $this->addColumn(
            'simulations_mail_points', 
            'scale_type_id', 
            "INT DEFAULT NULL COMMENT '1 - positive, 2 - negative, 3 - personal.'");
	}

	public function down()
	{
        $this->dropColumn('simulations_mail_points', 'scale_type_id');
	}
}

