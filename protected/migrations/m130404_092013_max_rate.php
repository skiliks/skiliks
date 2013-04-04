<?php

class m130404_092013_max_rate extends CDbMigration
{
	public function up()
	{
        $this->createTable('max_rate', [
            'id' => 'pk',
            'type' => 'varchar(50)',
            'rate' => 'int',
            'learning_goal_id' => 'int',
            'hero_behaviour_id' => 'int',
            'performance_rule_category_id' => 'VARCHAR(10)',
            'scenario_id' => 'int',
            'import_id' => 'VARCHAR(14) NOT NULL',
        ]);

        $this->addForeignKey('fk_max_rate_learning_goal_id', 'max_rate', 'learning_goal_id', 'learning_goal', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_max_rate_hero_behaviour_id', 'max_rate', 'hero_behaviour_id', 'hero_behaviour', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_max_rate_performance_rule_category_id', 'max_rate', 'performance_rule_category_id', 'activity_category', 'code', 'SET NULL', 'CASCADE');
        $this->addForeignKey('fk_max_rate_scenario_id', 'max_rate', 'scenario_id', 'scenario', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_max_rate_learning_goal_id', 'max_rate');
        $this->dropForeignKey('fk_max_rate_hero_behaviour_id', 'max_rate');
        $this->dropForeignKey('fk_max_rate_performance_rule_category_id', 'max_rate');
        $this->dropForeignKey('fk_max_rate_scenario_id', 'max_rate');

        $this->dropTable('max_rate');
	}
}