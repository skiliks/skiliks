<?php

class m130404_140621_performance_aggregated extends CDbMigration
{
	public function up()
	{
        $this->createTable('performance_aggregated', [
            'id' => 'pk',
            'sim_id' => 'int not null',
            'category_id' => 'varchar(10)',
            'value' => 'int',
            'percent' => 'int'
        ]);

        $this->addForeignKey('fk_performance_aggregated_sim_id', 'performance_aggregated', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_performance_aggregated_category_id', 'performance_aggregated', 'category_id', 'activity_category', 'code', 'SET NULL', 'CASCADE');
	}

	public function down()
	{
		$this->dropForeignKey('fk_performance_aggregated_sim_id', 'performance_aggregated');
		$this->dropForeignKey('fk_performance_aggregated_category_id', 'performance_aggregated');

        $this->dropTable('performance_aggregated');
	}
}