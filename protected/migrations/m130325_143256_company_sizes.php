<?php

class m130325_143256_company_sizes extends CDbMigration
{
	public function up()
	{
        $this->createTable('company_sizes', [
            'id' => 'pk',
            'label' => 'VARCHAR(120)'
        ]);

        $this->insert('company_sizes', ['label' => 'менее 10 человек']);
        $this->insert('company_sizes', ['label' => '10-50 человек']);
        $this->insert('company_sizes', ['label' => '50-100 человек']);
        $this->insert('company_sizes', ['label' => '100-500 человек']);
        $this->insert('company_sizes', ['label' => '500-1000 человек']);
        $this->insert('company_sizes', ['label' => '1000-5000 человек']);
        $this->insert('company_sizes', ['label' => '5000-10000 человек']);
        $this->insert('company_sizes', ['label' => 'более 10000 человек']);

        $this->addColumn('user_account_corporate', 'company_size_id', 'int');
        $this->addForeignKey(
            'fk_user_account_corporate_company_size_id',
            'user_account_corporate',
            'company_size_id',
            'company_sizes',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->addColumn('user_account_corporate', 'ownership_type', 'VARCHAR(50) DEFAULT NULL');
        $this->addColumn('user_account_corporate', 'company_name', 'VARCHAR(255) DEFAULT NULL');
        $this->addColumn('user_account_corporate', 'company_description', 'text');

        $this->delete('industry');

        $this->insert('industry', ['label' => 'Автомобильный бизнес']);
        $this->insert('industry', ['label' => 'Агропромышленный комплекс']);
        $this->insert('industry', ['label' => 'Финансовые услуги']);
        $this->insert('industry', ['label' => 'Информационные технологии, интернет, телеком']);
        $this->insert('industry', ['label' => 'Добыча и металлургия']);
        $this->insert('industry', ['label' => 'Лесная и деревообрабатывающая промышленность']);
        $this->insert('industry', ['label' => 'Машиностроение']);
        $this->insert('industry', ['label' => 'Медицина и фармацевтика']);
        $this->insert('industry', ['label' => 'Недвижимость, девелопмент, строительство']);
        $this->insert('industry', ['label' => 'Оборудование, приборостроение, электротехника']);
        $this->insert('industry', ['label' => 'Cтроительные материалы и оборудование']);
        $this->insert('industry', ['label' => 'Производство товаров массового спроса']);
        $this->insert('industry', ['label' => 'Розничная торговля']);
        $this->insert('industry', ['label' => 'Топливно-энергетический комплекс']);
        $this->insert('industry', ['label' => 'Транспорт и логистика']);
        $this->insert('industry', ['label' => 'Туризм, отдых, гостеприимство']);
        $this->insert('industry', ['label' => 'Химическая промышленность']);
        $this->insert('industry', ['label' => 'Профессиональные услуги']);
        $this->insert('industry', ['label' => 'Услуги населению']);
        $this->insert('industry', ['label' => 'Наука, образование']);
        $this->insert('industry', ['label' => 'Искусство, развлечения, масс-медиа']);
        $this->insert('industry', ['label' => 'Государственная служба, некоммерческие организации']);
        $this->insert('industry', ['label' => 'Авиация и космос']);
        $this->insert('industry', ['label' => 'Управляющие компании и холдинги']);
        $this->insert('industry', ['label' => 'Другая']);
	}

	public function down()
	{
        $this->dropForeignKey('fk_user_account_corporate_company_size_id', 'user_account_corporate');
        $this->dropColumn('user_account_corporate', 'company_size_id');
        $this->dropColumn('user_account_corporate', 'ownership_type');
        $this->dropColumn('user_account_corporate', 'company_name');
        //$this->dropColumn('user_account_corporate', 'company_description');

        $this->dropTable('company_sizes');
	}
}