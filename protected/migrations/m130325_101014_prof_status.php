<?php

class m130325_101014_prof_status extends CDbMigration
{
	public function up()
	{
        $this->renameTable('position', 'professional_statuses');
        $this->dropForeignKey('user_account_personal_FK_position', 'user_account_personal');
        $this->renameColumn('user_account_personal', 'position_id', 'professional_status_id');
        $this->addForeignKey(
            'fk_user_account_personal_professional_status_id',
            'user_account_personal',
            'professional_status_id',
            'professional_statuses',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->createTable('positions', [
            'id' => 'pk',
            'label' => 'VARCHAR(120) NOT NULL'
        ]);

        $this->addColumn('user_account_corporate', 'position_id', 'INT');
        $this->addForeignKey(
            'fk_user_account_corporate_position_id',
            'user_account_corporate',
            'position_id',
            'positions',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->insert('positions', ['label' => 'Собственник']);
        $this->insert('positions', ['label' => 'Высшее руководство']);
        $this->insert('positions', ['label' => 'Функциональный руководитель']);
        $this->insert('positions', ['label' => 'Проектный менеджер']);
        $this->insert('positions', ['label' => 'Функциональный специалист']);
        $this->insert('positions', ['label' => 'Руководитель HR']);
        $this->insert('positions', ['label' => 'Специалист HR']);
	}

	public function down()
	{
        $this->dropForeignKey('fk_user_account_corporate_position_id', 'user_account_corporate');
        $this->dropColumn('user_account_corporate', 'position_id');
        $this->dropTable('positions');

        $this->dropForeignKey('fk_user_account_personal_professional_status_id', 'user_account_personal');
        $this->renameColumn('user_account_personal', 'professional_status_id', 'position_id');
        $this->renameTable('professional_statuses', 'position');

        $this->addForeignKey(
            'user_account_personal_FK_position',
            'user_account_personal',
            'position_id',
            'position',
            'id',
            'SET NULL',
            'CASCADE'
        );
	}
}