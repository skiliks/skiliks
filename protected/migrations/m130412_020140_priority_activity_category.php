<?php

class m130412_020140_priority_activity_category extends CDbMigration
{
	public function up()
	{
        /* @var $activity ActivityCategory */
        $activity = ActivityCategory::model()->findByAttributes(['code'=>'0']);
        $activity->priority = 1;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'1']);
        $activity->priority = 2;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'2']);
        $activity->priority = 4;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'2_min']);
        $activity->priority = 3;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'3']);
        $activity->priority = 5;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'4']);
        $activity->priority = 6;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'5']);
        $activity->priority = 7;
        $activity->update();

	}

	public function down()
	{
        /* @var $activity ActivityCategory */
        $activity = ActivityCategory::model()->findByAttributes(['code'=>'0']);
        $activity->priority = 2;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'1']);
        $activity->priority = 3;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'2']);
        $activity->priority = 4;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'2_min']);
        $activity->priority = 1;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'3']);
        $activity->priority = 5;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'4']);
        $activity->priority = 6;
        $activity->update();

        $activity = ActivityCategory::model()->findByAttributes(['code'=>'5']);
        $activity->priority = 7;
        $activity->update();
	}
}