<?php

class m130122_151626_phone_time_call extends CDbMigration
{
	public function up()
	{
        $connection = $this->getDbConnection();
        $transaction = $connection->beginTransaction();
        try
        {
            $events_samples = $connection->createCommand("SELECT `e`.id, `e`.`trigger_time` FROM `events_samples` AS `e`")->queryAll();
            $this->alterColumn('events_samples', 'trigger_time', 'time DEFAULT NULL');
            foreach($events_samples as $event) {
                if(empty($event['trigger_time'])){
                    $trigger_time = null;
                }else{
                    $times = explode(':',$event['trigger_time']);
                    $datetime = new DateTime();
                    $datetime->setTime($times[0], $times[1], $times[2]);
                    $datetime->sub(new DateInterval("PT1H"));
                    $trigger_time = $datetime->format("H:i:s");
                }

                $this->update('events_samples', ['trigger_time'=>$trigger_time], "id = ".$event['id']);
            }
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
		echo "m130122_151626_phone_time_call does not support migration down.\n";
		return false;
	}

}