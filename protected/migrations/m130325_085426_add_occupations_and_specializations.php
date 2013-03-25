<?php

class m130325_085426_add_occupations_and_specializations extends CDbMigration
{
	public function up()
	{
        $occupations = [];
        $specializations = [];

        $rows = explode("\n", $this->data);
        foreach ($rows as $row) {
            $cols = explode("\t", $row);

            $occupations[$cols[0]] = $cols[0];
            $specializations[$cols[0]][] = $cols[1];
        }

        $occupationsId = [];
        foreach ($occupations as $occupation) {
            $obj = new ProfessionalOccupation();
            $obj->label = $occupation;

            echo "{$obj->label} \n";

            $obj->save();

            $occupationsId[$obj->label] = $obj->id;
        }
        unset($occupations);

        foreach ($specializations as $occupationLabel => $list) {
            $occupationId = $occupationsId[$occupationLabel];

            foreach ($list as $specialization) {
                $obj = new ProfessionalSpecialization();
                $obj->label                      = $specialization;
                $obj->professional_occupation_id = $occupationId;

                echo "$occupationLabel ({$obj->professional_occupation_id}) {$obj->label} \n";

                $obj->save();
            }
         }
	}

	public function down()
	{
		$this->truncateTable('professional_specialization');
		$this->truncateTable('professional_occupation');
	}

    public $data = 'Информационные технологии, интернет, телеком	CRM и ERP системы
Информационные технологии, интернет, телеком	Аналитика
Информационные технологии, интернет, телеком	Программное обеспечение
Информационные технологии, интернет, телеком	Интернет
Информационные технологии, интернет, телеком	Компьютерная безопасность
Информационные технологии, интернет, телеком	Консалтинг, Аутсорсинг
Информационные технологии, интернет, телеком	Контент
Информационные технологии, интернет, телеком	Маркетинг
Информационные технологии, интернет, телеком	Мультимедиа
Информационные технологии, интернет, телеком	Поддержка, Helpdesk
Информационные технологии, интернет, телеком	Программирование, разработка
Информационные технологии, интернет, телеком	Развитие бизнеса
Информационные технологии, интернет, телеком	Сетевые технологии
Информационные технологии, интернет, телеком	Системная интеграция
Информационные технологии, интернет, телеком	Телекоммуникации
Информационные технологии, интернет, телеком	Управление проектами
Бухгалтерия, управленческий учет, финансы предприятия	Аудит
Бухгалтерия, управленческий учет, финансы предприятия	Бухгалтерия
Бухгалтерия, управленческий учет, финансы предприятия	Бюджетирование и планирование
Бухгалтерия, управленческий учет, финансы предприятия	Валютный контроль
Бухгалтерия, управленческий учет, финансы предприятия	Казначейство
Бухгалтерия, управленческий учет, финансы предприятия	Касса, инкассация
Бухгалтерия, управленческий учет, финансы предприятия	МСФО, IFRS
Бухгалтерия, управленческий учет, финансы предприятия	Налоги
Бухгалтерия, управленческий учет, финансы предприятия	Финансовый анализ и контроль
Бухгалтерия, управленческий учет, финансы предприятия	Финансовый менеджмент
Бухгалтерия, управленческий учет, финансы предприятия	Ценные бумаги
Маркетинг, реклама, PR	Below The Line (BTL)
Маркетинг, реклама, PR	PR, Маркетинговые коммуникации
Маркетинг, реклама, PR	Аналитика
Маркетинг, реклама, PR	Бренд-менеджмент
Маркетинг, реклама, PR	Интернет-маркетинг
Маркетинг, реклама, PR	Исследования рынка
Маркетинг, реклама, PR	Маркетинг
Маркетинг, реклама, PR	Управление продуктом
Маркетинг, реклама, PR	Мерчендайзинг
Маркетинг, реклама, PR	Реклама
Маркетинг, реклама, PR	Продвижение
Маркетинг, реклама, PR	Управление проектами
Административный персонал	АХО
Административный персонал	Делопроизводство
Административный персонал	Сall-центр
Банки, инвестиции, лизинг	Денежные рынки
Банки, инвестиции, лизинг	Private Banking
Банки, инвестиции, лизинг	Розничный банковский бизнес
Банки, инвестиции, лизинг	Акции, Ценные бумаги
Банки, инвестиции, лизинг	Аналитика
Банки, инвестиции, лизинг	Мониторинг и контроль
Банки, инвестиции, лизинг	Бюджетирование
Банки, инвестиции, лизинг	Внутренние операции
Банки, инвестиции, лизинг	Инвестиции
Банки, инвестиции, лизинг	Управление ликвидностью
Банки, инвестиции, лизинг	Корпоративное финансирование
Банки, инвестиции, лизинг	Кредитование малого и среднего бизнеса
Банки, инвестиции, лизинг	Лизинг
Банки, инвестиции, лизинг	Методология, Банковские технологии
Банки, инвестиции, лизинг	Работа с проблемными заемщиками
Банки, инвестиции, лизинг	Разработка новых продуктов, Маркетинг
Банки, инвестиции, лизинг	Риски
Банки, инвестиции, лизинг	Торговое финансирование
Банки, инвестиции, лизинг	Трейдинг, Дилинг
Управление персоналом, тренинги	Компенсации и льготы
Управление персоналом, тренинги	Развитие персонала
Управление персоналом, тренинги	Рекрутмент
Управление персоналом, тренинги	Тренинги
Управление персоналом, тренинги	Управление персоналом
Управление персоналом, тренинги	Учет кадров
Автомобильный бизнес	Автозапчасти
Автомобильный бизнес	Дистрибуция
Автомобильный бизнес	Производство
Автомобильный бизнес	Прокат, лизинг
Автомобильный бизнес	Сервисное обслуживание
Безопасность	Взыскание задолженности, Коллекторская деятельность
Безопасность	Системы видеонаблюдения
Безопасность	Экономическая и информационная безопасность
Высший менеджмент	Администрирование
Высший менеджмент	Антикризисное управление
Высший менеджмент	Добыча cырья
Высший менеджмент	Инвестиции
Высший менеджмент	Информационные технологии, Интернет, Мультимедиа
Высший менеджмент	Искусство, Развлечения, Масс-медиа
Высший менеджмент	Коммерческий Банк
Высший менеджмент	Консультирование
Высший менеджмент	Маркетинг, Реклама, PR
Высший менеджмент	Медицина, Фармацевтика
Высший менеджмент	Наука, Образование
Высший менеджмент	Продажи
Высший менеджмент	Производство, Технология
Высший менеджмент	Спортивные клубы, Фитнес, Салоны красоты
Высший менеджмент	Страхование
Высший менеджмент	Строительство, Недвижимость
Высший менеджмент	Транспорт, Логистика
Высший менеджмент	Туризм, Гостиницы, Рестораны
Высший менеджмент	Управление закупками
Высший менеджмент	Управление малым бизнесом
Высший менеджмент	Управление персоналом, Тренинги
Высший менеджмент	Финансы
Высший менеджмент	Юриспруденция
Добыча сырья	Газ
Добыча сырья	Разведка и бурение
Добыча сырья	Нефть
Добыча сырья	Руда
Добыча сырья	Уголь
Искусство, развлечения, масс-медиа	Дизайн, графика, живопись, фотография
Искусство, развлечения, масс-медиа	Журналистика
Искусство, развлечения, масс-медиа	Издательская деятельность
Искусство, развлечения, масс-медиа	Кино, музыка
Искусство, развлечения, масс-медиа	Литературная, Редакторская деятельность
Искусство, развлечения, масс-медиа	Прочее
Искусство, развлечения, масс-медиа	Радио и телевидение
Консультирование	Информационные технологии
Консультирование	Корпоративные финансы
Консультирование	Управленческое консультирование
Медицина, фармацевтика	Ветеринария
Медицина, фармацевтика	Клинические исследования
Медицина, фармацевтика	Лекарственные препараты
Медицина, фармацевтика	Маркетинг
Медицина, фармацевтика	Медицинское оборудование
Медицина, фармацевтика	Оптика
Медицина, фармацевтика	Продажи
Медицина, фармацевтика	Производство
Медицина, фармацевтика	Психология
Наука, образование	Естественные науки
Наука, образование	Гуманитарные науки
Наука, образование	Точные науки
Наука, образование	Информатика, Информационные системы
Наука, образование	Экономика, Менеджмент
Государственная служба, некоммерческие организации	Муниципалитет
Государственная служба, некоммерческие организации	НИИ
Государственная служба, некоммерческие организации	Общественные организации
Государственная служба, некоммерческие организации	Правительство
Продажи	FMCG, товары массового спроса
Продажи	Автомобили, запчасти
Продажи	Потребительская электроника
Продажи	ГСМ, нефть, бензин
Продажи	Оптовая дистрибуция
Продажи	Программное обеспечение
Продажи	Мебель, сантехника
Продажи	Медицина, фармацевтика
Продажи	Металлы, металлопрокат
Продажи	Розничная торговля
Продажи	Сельское хозяйство
Продажи	Оборудование
Продажи	Строительные материалы
Продажи	Текстиль, одежда, обувь
Продажи	Телекоммуникации, сетевые решения
Продажи	Тендеры
Продажи	Товары для бизнеса
Продажи	Торговля биржевыми товарами
Продажи	Торговые сети
Продажи	Управление продажами
Продажи	Услуги для бизнеса
Продажи	Услуги для населения
Продажи	Финансовые услуги
Продажи	Химическая продукция
Производство	Авиационная промышленность
Производство	Автомобильная промышленность
Производство	Атомная энергетика
Производство	Деревообработка, Лесная промышленность
Производство	Закупки и снабжение
Производство	Конструкторское бюро
Производство	Контроль качества
Производство	Легкая промышленность
Производство	Машиностроение
Производство	Мебельное производство
Производство	Металлургия
Производство	Нефтепереработка
Производство	Охрана труда
Производство	Пищевая промышленность
Производство	Полиграфия
Производство	Радиоэлектронная промышленность
Производство	Сельхозпроизводство
Производство	Сертификация
Производство	Стройматериалы
Производство	Судостроение
Производство	Табачная промышленность
Производство	Управление проектами
Производство	Фармацевтическая промышленность
Производство	Химическая промышленность
Производство	Электроэнергетика
Производство	Ювелирная промышленность
Страхование	Автострахование
Страхование	Комплексное страхование физических лиц
Страхование	Комплексное страхование юридических лиц
Страхование	Медицинское страхование
Страхование	Перестрахование
Страхование	Страхование бизнеса
Страхование	Страхование жизни
Страхование	Страхование недвижимости
Страхование	Страхование ответственности
Страхование	Урегулирование убытков
Строительство, недвижимость	Водоснабжение и канализация
Строительство, недвижимость	Геодезия и картография
Строительство, недвижимость	Гостиницы, Магазины
Строительство, недвижимость	Девелопер
Строительство, недвижимость	Дизайн/Оформление
Строительство, недвижимость	ЖКХ
Строительство, недвижимость	Жилье
Строительство, недвижимость	Землеустройство
Строительство, недвижимость	Нежилые помещения
Строительство, недвижимость	Отопление, вентиляция и кондиционирование
Строительство, недвижимость	Оценка
Строительство, недвижимость	Проектирование, Архитектура
Строительство, недвижимость	Строительство
Строительство, недвижимость	Тендеры
Строительство, недвижимость	Управление проектами
Строительство, недвижимость	Эксплуатация
Транспорт, логистика	Авиаперевозки
Транспорт, логистика	Автоперевозки
Транспорт, логистика	Бизнес-авиация
Транспорт, логистика	ВЭД
Транспорт, логистика	Гражданская авиация
Транспорт, логистика	Железнодорожные перевозки
Транспорт, логистика	Закупки, Снабжение
Транспорт, логистика	Контейнерные перевозки
Транспорт, логистика	Логистика
Транспорт, логистика	Морские/Речные перевозки
Транспорт, логистика	Начальный уровень, Мало опыта
Транспорт, логистика	Складское хозяйство
Транспорт, логистика	Таможенное оформление
Транспорт, логистика	Трубопроводы
Туризм, гостиницы, рестораны	Авиабилеты
Туризм, гостиницы, рестораны	Банкеты
Туризм, гостиницы, рестораны	Кейтеринг
Туризм, гостиницы, рестораны	Организация встреч, Конференций
Туризм, гостиницы, рестораны	Организация туристических продуктов
Туризм, гостиницы, рестораны	Продажа туристических услуг
Туризм, гостиницы, рестораны	Управление гостиницами
Туризм, гостиницы, рестораны	Управление ресторанами, Барами
Туризм, гостиницы, рестораны	Управление туристическим бизнесом
Юриспруденция	Compliance
Юриспруденция	Авторское право
Юриспруденция	Адвокат
Юриспруденция	Антимонопольное право
Юриспруденция	Арбитраж
Юриспруденция	Банковское право
Юриспруденция	Взыскание задолженности, Коллекторская деятельность
Юриспруденция	Договорное право
Юриспруденция	Законотворчество
Юриспруденция	Земельное право
Юриспруденция	Интеллектуальная собственность
Юриспруденция	Корпоративное право
Юриспруденция	Международное право
Юриспруденция	Морское право
Юриспруденция	Налоговое право
Юриспруденция	Недвижимость
Юриспруденция	Недропользование
Юриспруденция	Регистрация юридических лиц
Юриспруденция	Семейное право
Юриспруденция	Слияния и поглощения
Юриспруденция	Страховое право
Юриспруденция	Трудовое право
Юриспруденция	Уголовное право
Юриспруденция	Ценные бумаги, Рынки капитала
Юриспруденция	Юрисконсульт
Закупки	FMCG, Товары народного потребления
Закупки	Автомобили, Запчасти
Закупки	ГСМ, нефть, бензин
Закупки	Потребительская электроника
Закупки	Металлопрокат
Закупки	Сертификация
Закупки	Станки, Тяжелое оборудование
Закупки	Строительные материалы
Закупки	Тендеры
Закупки	Товары для бизнеса
Закупки	Управление закупками
Закупки	Фармацевтика
Закупки	Химическая продукция
Закупки	Электротехническое оборудование/светотехника
Другое	Другое';
}