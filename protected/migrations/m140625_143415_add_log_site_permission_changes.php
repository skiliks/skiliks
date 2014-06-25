<?php

class m140625_143415_add_log_site_permission_changes extends CDbMigration
{
    public function up()
    {
        $this->createTable(
            'site_log_permission_changes',
            [
                'id'            => 'pk',
                'created_at'    => 'datetime',
                'initiator_id'  => 'int(10) UNSIGNED',
                'result'        => 'LONGTEXT',
            ]
        );

        $this->addForeignKey('site_log_permission_changes_fk_user',
            'site_log_permission_changes', 'initiator_id',
            'user', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('site_log_permission_changes_fk_user', 'site_log_permission_changes');
        $this->dropTable('site_log_permission_changes');
    }
}