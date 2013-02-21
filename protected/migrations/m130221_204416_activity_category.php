<?php

class m130221_204416_activity_category extends CDbMigration
{
    public function up()
    {
        $this->createTable('activity_category', [
            'code'=>'VARCHAR(10) NOT NULL PRIMARY KEY',
            'priority'=>'int not null'
        ]);
        $this->insert('activity_category', ['code'=>'2_min', 'priority'=>1]);
        $this->insert('activity_category', ['code'=>'0', 'priority'=>2]);
        $this->insert('activity_category', ['code'=>'1', 'priority'=>3]);
        $this->insert('activity_category', ['code'=>'2', 'priority'=>4]);
        $this->insert('activity_category', ['code'=>'3', 'priority'=>5]);
        $this->insert('activity_category', ['code'=>'4', 'priority'=>6]);
        $this->insert('activity_category', ['code'=>'5', 'priority'=>7]);
        $this->addForeignKey('fk_activity_category_id', 'activity', 'category_id', 'activity_category', 'code',
            'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_activity_category_id', 'activity_category');
        $this->dropTable('activity_category');
        $this->alterColumn('activity','category_id','VARCHAR(10) DEFAULT NULL');
    }
}