<?php

class m130325_172550_add_tariff extends CDbMigration
{
	public function up()
	{
        $this->createTable('tariff', [
            'id'       => 'pk',
            'label'    => 'VARCHAR(120) NOT NULL',
            'is_free'  => 'TINYINT(1) DEFAULT 0',
            'price'    => 'DECIMAL (10,2) NOT NULL',
            'safe_amount' => 'DECIMAL (10,2) NOT NULL DEFAULT 0',
            'currency' => 'VARCHAR(3) NOT NULL DEFAULT \'RUB\'',
            'simulations_amount' => 'INT DEFAULT 0',
            'description' => 'TEXT DEFAULT NULL', // comma separated benefits
            'benefits' => 'TEXT DEFAULT NULL',
            'order'    => 'INT DEFAULT NULL', // display order
        ]);

        $this->insert('tariff', [
            'label' => 'Пробный',
            'is_free' => true,
            'price' => ' ',
            'simulations_amount' => 3,
            'order' => 1,
        ]);

        $this->insert('tariff', [
            'label' => 'Малый',
            'price' => 629,
            'simulations_amount' => 10,
            'benefits' => 'Вариативный сценарий, регулярные обновления',
            'order' => 2,
        ]);

        $this->insert('tariff', [
            'label' => 'Профессиональный',
            'price' => 1888,
            'safe_amount' => 1257,
            'simulations_amount' => 50,
            'benefits' => 'Вариативный сценарий, регулярные обновления',
            'order' => 3,
        ]);

        $this->insert('tariff', [
            'label' => 'Бизнес',
            'price' => 4999,
            'safe_amount' => 7581,
            'simulations_amount' => 50,
            'benefits' => 'Вариативный сценарий, регулярные обновления',
            'order' => 4,
        ]);

        $this->addColumn('user_account_corporate', 'tariff_id', 'INT DEFAULT NULL');
        $this->addColumn('user_account_corporate', 'tariff_activated_at', 'DATETIME DEFAULT NULL');
        $this->addColumn('user_account_corporate', 'tariff_expired_at', 'DATETIME DEFAULT NULL');

        $this->addForeignKey(
            'user_account_corporate_fk_tariff',
            'user_account_corporate',
            'tariff_id',
            'tariff',
            'id',
            'SET NULL',
            'CASCADE'
        );
	}

	public function down()
	{
		$this->dropForeignKey('user_account_corporate_fk_tariff', 'user_account_corporate');
		$this->dropColumn('user_account_corporate', 'tariff_expired_at');
		$this->dropColumn('user_account_corporate', 'tariff_activated_at');
		$this->dropColumn('user_account_corporate', 'tariff_id');
		$this->dropTable('tariff');
	}
}