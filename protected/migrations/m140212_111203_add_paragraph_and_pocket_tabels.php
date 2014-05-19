<?php

class m140212_111203_add_paragraph_and_pocket_tabels extends CDbMigration
{
	public function up()
	{
        $this->createTable('paragraph',[
            'id' => 'pk',
            'scenario_id' => 'int(11) NOT NULL',
            'alias' => 'varchar(255) default null',
            'label' => 'varchar(255) default null',
            'order_number' => 'int(11) default null',
            'value_1' => 'varchar(255) default null',
            'value_2' => 'varchar(255) default null',
            'value_3' => 'varchar(255) default null',
            'method' => 'varchar(255) default null',
        ]);

        $this->addForeignKey('fk_paragraph_scenario_id', 'paragraph', 'scenario_id',
            'scenario', 'id', 'CASCADE', 'CASCADE');

        $this->createTable('paragraph_pocket',[
            'id' => 'pk',
            'scenario_id' => 'int(11) NOT NULL',
            'paragraph_alias' => 'varchar(255) default null',
            'behaviour_alias' => 'varchar(255) default null',
            'left_direction' => 'varchar(255) default null',
            'left' => 'float default null',
            'right_direction' => 'varchar(255) default null',
            'right' => 'float default null',
            'text' => 'text default null',
        ]);

        $this->addForeignKey('fk_paragraph_pocket_scenario_id', 'paragraph_pocket', 'scenario_id',
            'scenario', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
        $this->dropForeignKey('fk_paragraph_pocket_scenario_id', 'paragraph_pocket');
        $this->dropForeignKey('fk_paragraph_scenario_id', 'paragraph');
        $this->dropTable('paragraph');
        $this->dropTable('paragraph_pocket');

	}

}