<?php

class m130131_122919_communication_theme_mail_prefix extends CDbMigration
{
    public function up()
    {
        $this->addColumn('communication_themes', 'code', 'integer NOT NULL');
        $this->createTable('mail_prefix', [
            'code' => 'string NOT NULL PRIMARY KEY',
            'title' => 'string NOT NULL'
        ]);
        $this->addColumn('communication_themes', 'mail_prefix', 'string');
        $this->update('communication_themes', ['mail_prefix' => null]);
        $this->addForeignKey(
            'communication_themes_mail_prefix', 'communication_themes', 'mail_prefix', 'mail_prefix', 'code',
            'CASCADE', 'CASCADE');
        $this->insert('mail_prefix', [
            'code' => 're', 'title' => 'Re:'
        ]);
        $this->insert('mail_prefix', [
            'code' => 'double_re', 'title' => 'Re: Re:'
        ]);
        $this->insert('mail_prefix',[
            'code' => 'triple_re', 'title' => 'Re: Re: Re:'
        ]);
        $this->insert('mail_prefix',[
            'code' => 'fwd', 'title' => 'Fwd:'
        ]);
    }

    public function down()
    {
        $this->dropForeignKey('communication_themes_mail_prefix', 'communication_themes');
        $this->dropColumn('communication_themes', 'mail_prefix');
        $this->dropColumn('communication_themes', 'code');
        $this->dropTable('mail_prefix');
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}