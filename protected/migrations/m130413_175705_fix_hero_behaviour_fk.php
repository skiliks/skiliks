<?php

class m130413_175705_fix_hero_behaviour_fk extends CDbMigration
{
	public function up()
	{
//        $this->dropForeignKey('hero_behaviour_learning_goal', 'hero_behaviour');
//
//        $this->addForeignKey(
//            'hero_behaviour_learning_goal',
//            'hero_behaviour',
//            'learning_goal_id',
//            'learning_goal',
//            'id',
//            'CASCADE',
//            'CASCADE'
//        );
	}

	public function down()
	{

	}
}