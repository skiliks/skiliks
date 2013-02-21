<?php

class m130220_150426_parent_activity extends CDbMigration
{
    public function up()
    {
        $this->createTable('activity_parent', [
            'id'    => 'VARCHAR(10) NOT NULL PRIMARY KEY',
            'import_id' => 'VARCHAR(14)'
        ]);
    }

    public function down()
    {
        $this->dropTable('activity_parent');
    }
}