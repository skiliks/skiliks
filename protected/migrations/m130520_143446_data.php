<?php

class m130520_143446_data extends CDbMigration
{
	public function up()
	{
        $status = ProfessionalStatus::model()->findByAttributes(['label'=>'Линейный менеджер']);
        $status->label = 'Функциональный менеджер';
        $status->update();
        unset($status);

        $status = ProfessionalStatus::model()->findByAttributes(['label'=>'Основной персонал, специалист']);
        $status->label = 'Специалист';
        $status->update();
        unset($status);

        $status = ProfessionalStatus::model()->findByAttributes(['label'=>'Вспомогательный персонал']);
        $status->delete();
        unset($status);

	}

	public function down()
	{
		echo "m130520_143446_data does not support migration down.\n";
		return false;
	}

}