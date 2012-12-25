<?php

class m121223_173012_fix_for_FK extends CDbMigration
{
	public function up()
	{
        $this->dropForeignKey('assessment_aggregated_FK_character_point_title', 'assessment_aggregated');
        $this->addForeignKey(
            'assessment_aggregated_FK_character_point_title', 
            'assessment_aggregated',
            'point_id', 
            'characters_points_titles', 
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