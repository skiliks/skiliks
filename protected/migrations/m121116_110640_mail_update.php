<?php

class m121116_110640_mail_update extends CDbMigration
{

	public function up()
	{
        $transaction = $this->getDbConnection()->beginTransaction();
        try
        {
            $this->delete('mail_box');
            $this->insert('mail_group', array('id'=>5, 'name'=>'не пришло'));
            $this->alterColumn('mail_box', 'group_id', 'INT(11) NOT NULL DEFAULT 5');
            $this->addColumn('mail_box', 'type', "TINYINT NOT NULL DEFAULT 0 COMMENT 'Столбец нужен для типа сообщения 1 - Входящие, 2 - Исходящие, 3 - Входящие(доставлен), 4 - Исходящие(доставлен)'  AFTER `sending_time`");
            $this->addColumn('mail_box', 'plan', "TINYINT NOT NULL DEFAULT 0 COMMENT 'Cтолбец reply для обозначения был ответ(1) или нет(0)'  AFTER `type`");
            $this->addColumn('mail_box', 'reply', "TINYINT NOT NULL DEFAULT 0 COMMENT 'mail_box.plan состояние плана для для письма, 0 - не запланировано, 1- заплпнировано'  AFTER `plan`");
            $this->alterColumn('mail_template', 'group_id', 'INT(11) NOT NULL DEFAULT 5');
            $this->addColumn('mail_template', 'type', "TINYINT NOT NULL DEFAULT 0 COMMENT 'Столбец нужен для типа сообщения 1 - Входящие, 2 - Исходящие, 3 - Входящие(доставлен), 4 - Исходящие(доставлен)'  AFTER `sending_time`");

            $importService = new ImportGameDataService();
            $result = $importService->importEmails(); 
            
            $transaction->commit();
        }
        catch(Exception $e)
        {
            echo "Exception: ".$e->getMessage()."\n";
            $transaction->rollback();
            return false;
        }
        
	}

	public function down()
	{
        $this->delete('mail_group', 'where `id` = 5');
        $this->dropColumn('mail_box', 'type');
        $this->dropColumn('mail_box', 'plan');
        $this->dropColumn('mail_box', 'reply');
        $this->alterColumn('mail_template', 'group_id', 'INT(11) DEFAULT NULL');
        $this->dropColumn('mail_template', 'type');
        $this->alterColumn('mail_group', 'id', 'INT(11) NOT NULL');
	}

}