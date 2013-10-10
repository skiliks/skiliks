<?php

class m131010_101243_adding_ip_adress_to_feedback_table extends CDbMigration
{

    public function up()
    {
        $this->addColumn("feedback", "ip_address", "varchar (15) DEFAULT NULL");
    }

    public function down()
    {
        $this->dropColumn("feedback","ip_address");
    }

}