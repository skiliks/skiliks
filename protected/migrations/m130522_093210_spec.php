<?php

class m130522_093210_spec extends CDbMigration
{
	public function up()
	{
        $this->getDbConnection()->createCommand('SET FOREIGN_KEY_CHECKS=0;')->execute();
        $this->dropForeignKey('professional_specialization_fk_professional_occupation', 'professional_specialization');
        $this->dropColumn('professional_specialization', 'professional_occupation_id');
        $this->truncateTable("professional_specialization");
        $list = [
            'Развитие бизнеса',
            'Управление продуктами',
            'PR, Маркетинг, продвижение',
            'Исследования, разработки',
            'Аналитика',
            'Продажи',
            'Колл-центр',
            'Производство',
            'Оказание услуг',
            'Cоздание контента',
            'Редактура, художественное оформление',
            'Операционная деятельность',
            'Постродажное обслуживание',
            'Трейдинг',
            'Транспортировка и хранение',
            'Закупки',
            'Информационные технологии',
            'Управление персоналом',
            'Управление рисками',
            'Бюджетирование и планирование',
            'Финансовый менеджмент',
            'Учёт, налоги, отчетность',
            'Мониторинг и контроль',
            'Юридическая поддержка',
            'Безопасность',
            'Методология, оптимизация',
            'Административно-хозяйственная деятельность',
            'Документооборот',
            'Управление проектами',
            'Прочее'];
        foreach($list as $item) {
            $spec = new ProfessionalSpecialization();
            $spec->label = $item;
            $spec->save(false);
            echo $item."\r\n";
        }

        $default = ProfessionalSpecialization::model()->findByAttributes(['label'=>'Аналитика']);

        $vacancies = Vacancy::model()->findAll();
        foreach($vacancies as $vacancy){
            /* @var Vacancy $vacancy */
            $vacancy->professional_specialization_id = $default->id;
            $vacancy->save();
        }

        $this->getDbConnection()->createCommand('SET FOREIGN_KEY_CHECKS=1;')->execute();
	}

	public function down()
	{
		echo "m130522_093210_spec does not support migration down.\n";
		return true;
	}

}