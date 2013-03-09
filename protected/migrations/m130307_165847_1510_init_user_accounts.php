<?php

class m130307_165847_1510_init_user_accounts extends CDbMigration
{
	public function up()
	{
        $this->createTable('position', [
            'id'       => 'INT(11)',
            'language' => 'VARCHAR(2)', // ISO2
            'label'    => 'VARCHAR(120)',
        ]);

        $this->createIndex('position_I_id_language', 'position', 'id,language', true);

        $this->createTable('industry', [
            'id'       => 'INT(11)',
            'language' => 'VARCHAR(2)', // ISO2
            'label'    => 'VARCHAR(120)'
        ]);

        $this->createIndex('industry_I_id_language', 'industry', 'id,language', true);

        $this->createTable('user_account_personal', [
            'user_id'     => 'INT(10) UNSIGNED NOT NULL PRIMARY KEY',
            'industry_id' => 'INT(11)',
            'position_id' => 'INT(11)',
        ]);

        $this->createTable('user_account_corporate', [
            'user_id'     => 'INT(10) UNSIGNED NOT NULL PRIMARY KEY',
            'industry_id' => 'INT(11)',
        ]);

        $this->addForeignKey(
            'user_account_personal_FK_position',
            'user_account_personal',
            'position_id',
            'position',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_account_personal_FK_industry',
            'user_account_personal',
            'industry_id',
            'industry',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_account_corporate_FK_industry',
            'user_account_corporate',
            'industry_id',
            'industry',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_account_personal_FK_user',
            'user_account_personal',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'user_account_corporate_FK_user',
            'user_account_corporate',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->insert('industry', [
            'id'       => 1,
            'language' => 'ru',
            'label'    => 'Банковское дело',
        ]);

        $this->insert('industry', [
            'id'       => 1,
            'language' => 'en',
            'label'    => 'Banking',
        ]);

        $this->insert('industry', [
            'id'       => 2,
            'language' => 'ru',
            'label'    => 'Страхование',
        ]);

        $this->insert('industry', [
            'id'       => 2,
            'language' => 'en',
            'label'    => 'Insurance',
        ]);

        $this->insert('position', [
            'id'       => 1,
            'language' => 'ru',
            'label'    => 'Линейный менеджер',
        ]);

        $this->insert('position', [
            'id'       => 1,
            'language' => 'en',
            'label'    => 'Linear manager',
        ]);

        $this->insert('position', [
            'id'       => 2,
            'language' => 'ru',
            'label'    => 'Начальник отдела аналитики',
        ]);

        $this->insert('position', [
            'id'       => 2,
            'language' => 'en',
            'label'    => 'Analytics department manager',
        ]);
	}

	public function down()
	{
		$this->dropForeignKey('user_account_corporate_FK_user','user_account_corporate');
		$this->dropForeignKey('user_account_personal_FK_user','user_account_personal');
		$this->dropForeignKey('user_account_corporate_FK_industry','user_account_corporate');
		$this->dropForeignKey('user_account_personal_FK_industry','user_account_personal');
		$this->dropForeignKey('user_account_personal_FK_position','user_account_personal');

        $this->dropIndex('industry_I_id_language','industry');
        $this->dropIndex('position_I_id_language','position');

        $this->dropTable('user_account_personal');
        $this->dropTable('user_account_corporate');
        $this->dropTable('industry');
        $this->dropTable('position');
	}
}