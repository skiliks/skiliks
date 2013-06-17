<?php

class m130522_074455_industry extends CDbMigration
{
	public function safeUp()
	{
       $list = [
        'Автомобильный бизнес',
        'Агропромышленный комплекс',
        'Финансовые услуги',
        'Информационные технологии, интернет, телеком',
        'Добыча и металлургия',
        'Лесная и деревообрабатывающая промышленность',
        'Машиностроение',
        'Медицина и фармацевтика',
        'Недвижимость, девелопмент, строительство',
        'Оборудование, приборостроение, электротехника',
        'Строительные материалы и оборудование',
        'Производство товаров массового спроса',
        'Розничная торговля',
        'Топливно-энергетический комплекс',
        'Транспорт и логистика',
        'Туризм, отдых, гостеприимство',
        'Химическая промышленность',
        'Профессиональные услуги',
        'Услуги населению',
        'Наука, образование',
        'Искусство, развлечения, масс-медиа',
        'Государственная служба, некоммерческие организации',
        'Авиация и космос',
        'Управляющие компании и холдинги',
        'Другая'
       ];
        //$this->addColumn("professional_occupation", "order_no", 'INT(11) DEFAULT 0');
        //$this->truncateTable("professional_occupation");
        $arr = ProfessionalOccupation::model()->findAll();
        foreach($list as $key => $value){
            if(isset($arr[$key])){
                $arr[$key]->label = $value;
                $arr[$key]->save();
            }else{
                $el = new ProfessionalOccupation();
                $el->label = $value;
                $el->save();
            }
        }
	}

	public function safeDown()
	{
        $data ="UPDATE skiliks.professional_occupation SET label = 'Информационные технологии, интернет, телеком' WHERE id = 1;
                UPDATE skiliks.professional_occupation SET label = 'Бухгалтерия, управленческий учет, финансы предприятия' WHERE id = 2;
                UPDATE skiliks.professional_occupation SET label = 'Маркетинг, реклама, PR' WHERE id = 3;
                UPDATE skiliks.professional_occupation SET label = 'Административный персонал' WHERE id = 4;
                UPDATE skiliks.professional_occupation SET label = 'Банки, инвестиции, лизинг' WHERE id = 5;
                UPDATE skiliks.professional_occupation SET label = 'Управление персоналом, тренинги' WHERE id = 6;
                UPDATE skiliks.professional_occupation SET label = 'Автомобильный бизнес' WHERE id = 7;
                UPDATE skiliks.professional_occupation SET label = 'Безопасность' WHERE id = 8;
                UPDATE skiliks.professional_occupation SET label = 'Высший менеджмент' WHERE id = 9;
                UPDATE skiliks.professional_occupation SET label = 'Добыча сырья' WHERE id = 10;
                UPDATE skiliks.professional_occupation SET label = 'Искусство, развлечения, масс-медиа' WHERE id = 11;
                UPDATE skiliks.professional_occupation SET label = 'Консультирование' WHERE id = 12;
                UPDATE skiliks.professional_occupation SET label = 'Медицина, фармацевтика' WHERE id = 13;
                UPDATE skiliks.professional_occupation SET label = 'Наука, образование' WHERE id = 14;
                UPDATE skiliks.professional_occupation SET label = 'Государственная служба, некоммерческие организации' WHERE id = 15;
                UPDATE skiliks.professional_occupation SET label = 'Продажи' WHERE id = 16;
                UPDATE skiliks.professional_occupation SET label = 'Производство' WHERE id = 17;
                UPDATE skiliks.professional_occupation SET label = 'Страхование' WHERE id = 18;
                UPDATE skiliks.professional_occupation SET label = 'Строительство, недвижимость' WHERE id = 19;
                UPDATE skiliks.professional_occupation SET label = 'Транспорт, логистика' WHERE id = 20;
                UPDATE skiliks.professional_occupation SET label = 'Туризм, гостиницы, рестораны' WHERE id = 21;
                UPDATE skiliks.professional_occupation SET label = 'Юриспруденция' WHERE id = 22;
                UPDATE skiliks.professional_occupation SET label = 'Закупки' WHERE id = 23;
                UPDATE skiliks.professional_occupation SET label = 'Другое' WHERE id = 24;";
        $this->getDbConnection()->createCommand($data)->execute();
	}
}