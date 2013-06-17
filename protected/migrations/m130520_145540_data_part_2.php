<?php

class m130520_145540_data_part_2 extends CDbMigration
{
	public function safeUp()
	{
        $status = ProfessionalSpecialization::model()->findByAttributes(['label'=>'Оказание услуг']);
        if($status === null){
            $status = new ProfessionalSpecialization();
            $status->label = 'Оказание услуг';
            $status->save();
        }
        unset($status);

        $status = ProfessionalSpecialization::model()->findByAttributes(['label'=>'Cоздание контента']);
        if($status === null){
            $status = new ProfessionalSpecialization();
            $status->label = 'Cоздание контента';
            $status->save();
        }
        unset($status);

        $status = ProfessionalSpecialization::model()->findByAttributes(['label'=>'Операционная деятельность']);
        if($status === null){
            $status = new ProfessionalSpecialization();
            $status->label = 'Операционная деятельность';
            $status->save();
        }
        unset($status);

        $status = ProfessionalSpecialization::model()->findByAttributes(['label'=>'Постродажное обслуживание']);
        if($status === null){
            $status = new ProfessionalSpecialization();
            $status->label = 'Постродажное обслуживание';
            $status->save();
        }
        unset($status);

        $status = ProfessionalSpecialization::model()->findByAttributes(['label'=>'Трейдинг']);
        if($status === null){
            $status = new ProfessionalSpecialization();
            $status->label = 'Трейдинг';
            $status->save();
        }
        unset($status);

        $status = ProfessionalSpecialization::model()->findByAttributes(['label'=>'Транспортировка и хранение']);
        if($status === null){
            $status = new ProfessionalSpecialization();
            $status->label = 'Транспортировка и хранение';
            $status->save();
        }
        unset($status);
	}

	public function down()
	{
		echo "m130520_145540_data_part_2 does not support migration down.\n";
		return false;
	}

}