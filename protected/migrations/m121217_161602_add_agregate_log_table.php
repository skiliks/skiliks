<?php

/**
 *
 * @author slavka
 */
class m121217_161602_add_agregate_log_table extends CDbMigration
{
	public function up()
	{
        $this->createTable(
            'assassment_agregated', 
            array(
                'id'       => "pk",
                'sim_id' => "INT NOT NULL",
                'point_id' => "INT NOT NULL",
                'value'    => "FLOAT",
                )
            );
        $this->createIndex('assassment_agregated_I_id'      , 'assassment_agregated', 'id');
        $this->createIndex('assassment_agregated_I_point_id', 'assassment_agregated', 'point_id');
        $this->createIndex('assassment_agregated_I_sim_id'  , 'assassment_agregated', 'sim_id');
        $this->addForeignKey(
            'assassment_agregated_FK_character_point_title', 
            'assassment_agregated',
            'point_id', 
            'characters_points_titles', 
            'id'
        );
        $this->addForeignKey(
            'assassment_agregated_FK_simulations', 
            'assassment_agregated',
            'sim_id', 
            'simulations', 
            'id'
        );
    }

	public function down()
	{
        $this->dropIndex('assassment_agregated_I_id'                         , 'assassment_agregated');
        $this->dropForeignKey('assassment_agregated_FK_character_point_title', 'assassment_agregated');
        $this->dropIndex('assassment_agregated_I_point_id'                   , 'assassment_agregated');     
        $this->dropForeignKey('assassment_agregated_FK_simulations'          , 'assassment_agregated');
        $this->dropIndex('assassment_agregated_I_sim_id'                     , 'assassment_agregated'); 
        $this->dropTable('assassment_agregated');
	}
}

