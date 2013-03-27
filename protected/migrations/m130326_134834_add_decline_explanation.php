<?php

class m130326_134834_add_decline_explanation extends CDbMigration
{
	public function up()
	{
        $this->createTable('decline_explanation', [
            'id'                  => 'pk',
            'invite_id'           => 'INT DEFAULT NULL',
            'invite_recipient_id' => 'INT UNSIGNED DEFAULT NULL',
            'invite_owner_id'     => 'INT UNSIGNED DEFAULT NULL',
            'vacancy_label'       => 'INT DEFAULT NULL',
            'reason_id'           => 'INT NOT NULL',
            'description'         => 'TEXT NOT NULL',
            'created_at'          => 'DATETIME DEFAULT NULL',
        ]);

        $this->createTable('decline_reason', [
            'id'         => 'pk',
            'label'      => 'VARCHAR(120) NOT NULL',
            'sort_order' => 'INT DEFAULT 0',
            'is_display' => 'TINYINT(1) DEFAULT 1'
        ]);

        $this->addColumn('invites', 'simulation_id', 'INT');

        $this->addForeignKey(
            'invites_fk_simulation_id',
            'invites',
            'simulation_id',
            'simulations',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->insert('decline_reason', [
            'label' => 'Не хочу регистрироваться',
            'sort_order' => 0,
        ]);

        $this->insert('decline_reason', [
            'label' => 'Не интересует вакансия',
            'sort_order' => 0,
        ]);

        $this->insert('decline_reason', [
            'label' => 'Не хочу проходить тест',
            'sort_order' => 0,
        ]);

        $this->insert('decline_reason', [
            'label' => 'Другое',
            'sort_order' => 0,
        ]);

        $this->addForeignKey(
            'decline_explanation_fk_invite',
            'decline_explanation',
            'invite_id',
            'invites',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'decline_explanation_fk_recipient_id',
            'decline_explanation',
            'invite_recipient_id',
            'user',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'decline_explanation_fk_invite_owner_id',
            'decline_explanation',
            'invite_owner_id',
            'user',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'decline_explanation_fk_decline_reason_id',
            'decline_explanation',
            'reason_id',
            'decline_reason',
            'id',
            'CASCADE',
            'CASCADE'
        );
	}

	public function down()
	{
        $this->dropForeignKey('decline_explanation_fk_invite', 'decline_explanation');
        $this->dropForeignKey('decline_explanation_fk_recipient_id', 'decline_explanation');
        $this->dropForeignKey('decline_explanation_fk_invite_owner_id', 'decline_explanation');
        $this->dropForeignKey('decline_explanation_fk_decline_reason_id', 'decline_explanation');
        $this->dropForeignKey('invites_fk_simulation_id', 'invites');

        $this->dropColumn('invites', 'simulation_id');

		$this->dropTable('decline_explanation');
        $this->dropTable('decline_reason');
	}
}