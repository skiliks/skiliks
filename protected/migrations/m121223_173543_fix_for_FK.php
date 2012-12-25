<?php

class m121223_173543_fix_for_FK extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('assessment_aggregated_FK_simulations', 'assessment_aggregated');
        $this->addForeignKey(
            'assessment_aggregated_FK_simulations', 
            'assessment_aggregated',
            'sim_id', 
            'simulations', 
            'id',
            'CASCADE',
            'CASCADE'
        );
	}

	public function down()
	{
        // no down migration
	}
}