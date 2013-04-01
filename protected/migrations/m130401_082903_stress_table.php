<?php

class m130401_082903_stress_table extends CDbMigration
{
	public function up()
	{
        $this->createTable('stress_rule', [
            'id' => 'pk',
            'replica_id' => 'INT',
            'mail_id' => 'INT',
            'value' => 'INT',
            'import_id' => 'VARCHAR(14) DEFAULT NULL'
        ]);

        $this->createTable('stress_point', [
            'id' => 'pk',
            'sim_id' => 'INT NOT NULL',
            'stress_rule_id' => 'INT NOT NULL'
        ]);

        $this->addForeignKey('fk_stress_rule_replica_id', 'stress_rule', 'replica_id', 'replica', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_stress_rule_mail_id', 'stress_rule', 'mail_id', 'mail_template', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk_stress_point_sim_id', 'stress_point', 'sim_id', 'simulations', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_stress_point_stress_rule_id', 'stress_point', 'stress_rule_id', 'stress_rule', 'id', 'CASCADE', 'CASCADE');
	}

	public function down()
	{
		$this->dropForeignKey('fk_stress_rule_replica_id', 'stress_rule');
		$this->dropForeignKey('fk_stress_rule_mail_id', 'stress_rule');

        $this->dropForeignKey('fk_stress_point_sim_id', 'stress_point');
        $this->dropForeignKey('fk_stress_point_stress_rule_id', 'stress_point');

        $this->dropTable('stress_rule');
        $this->dropTable('stress_point');
	}
}