<?php

/**
 * Класс генерации Гороскопа
 */
class SimulationResultTextService {

    /**
     * Карманы
     * @var array
     */
    public static $pockets = [];

    /**
     * Результат генерации(текста гороскопа)
     * @var array
     */
    public static $recommendations = [];

    /**
     * Метод генерирует Гороскоп
     * @param Simulation $simulation
     * @param string $type Тип popup или pdf
     * @return array
     * @throws Exception
     */
    public static function generate(Simulation $simulation, $type, $part_assessment = false) {
        foreach(ParagraphPocket::model()->findAll() as $pocket) {
            /* @var $pocket ParagraphPocket */
            self::$pockets[$pocket->paragraph_alias][$pocket->behaviour_alias][] = $pocket;
        }
        $assessment = json_decode($simulation->getAssessmentDetails(), true);
        /* @var $paragraphs Paragraph[] */
        $paragraphs = Paragraph::model()->findAll('scenario_id = '.$simulation->game_type->id. ' and type = \''.$type.'\' order by order_number');
        $value = null;
        foreach($paragraphs as $paragraph) {
            try{
                switch($paragraph->method)
                {
                    case 'SinglePocket':
                        self::$recommendations[$paragraph->alias] = self::SinglePocket($paragraph->value_1, $paragraph->alias, $assessment);
                        break;
                    case 'TwoPocketsWithOneNegative':
                        self::$recommendations[$paragraph->alias] = self::TwoPocketsWithOneNegative($paragraph->value_1, $paragraph->value_2, $paragraph->alias, $assessment);
                        break;
                    case 'ThreePocketsWithTwoNegative':
                        self::$recommendations[$paragraph->alias] = self::ThreePocketsWithTwoNegative($paragraph->value_1, $paragraph->value_2, $paragraph->value_3, $paragraph->alias, $assessment);
                        break;
                    default:
                        throw new Exception("Метод {$paragraph->method}");
                        break;
                }
            }
            catch(AssessmentValueNotFound $e) {
                if($part_assessment) {
                    continue;
                } else {
                    throw new Exception($e->getMessage());
                }
            }

        }

        return self::$recommendations;
    }

    /**
     * Ищет текст по значению $behaviour_alias_1 для повидения $alias по карману
     * @param $behaviour_alias_1
     * @param $alias
     * @param $assessment
     * @return string
     * @throws Exception
     */
    public static function SinglePocket($behaviour_alias_1, $alias, $assessment) {
        $value_1 = self::getValueInAssessment($behaviour_alias_1, $assessment);

        $pockets = self::$pockets[$alias][$behaviour_alias_1];
        /* @var $pockets ParagraphPocket[] */
        foreach($pockets as $pocket) {
            $left_direction = trim($pocket->left_direction);
            $right_direction = trim($pocket->right_direction);
            if(self::$left_direction($pocket->left, $value_1) && self::$right_direction($pocket->right, $value_1)){
                return $pocket->text;
            }
        }

        throw new Exception("Карман не найден");

    }

    /**
     * Ищет текст по значению $behaviour_alias_2(негативное) для повидения $alias по карману
     * или для позитивноего и негативного
     * @param $behaviour_alias_1
     * @param $behaviour_alias_2
     * @param $alias
     * @param $assessment
     * @return string
     * @throws Exception
     */
    public static function TwoPocketsWithOneNegative($behaviour_alias_1, $behaviour_alias_2, $alias, $assessment) {
        $value_2 = self::getValueInAssessment($behaviour_alias_2, $assessment);

        $pockets_2 = self::$pockets[$alias][$behaviour_alias_1];
        /* @var $pockets_2 ParagraphPocket[] */
        foreach($pockets_2 as $pocket_num => $pocket) {
            $left_direction = trim($pocket->left_direction);
            $right_direction = trim($pocket->right_direction);
            if(self::$left_direction($pocket->left, $value_2) && self::$right_direction($pocket->right, $value_2)){
                //Если человек мало сделал ошибок(первый карман) то получает positive
                if($pocket_num === 0) {
                    return self::SinglePocket($behaviour_alias_1, $alias, $assessment);
                } elseif($pocket_num === (count($pockets_2) - 1)) { //Если много(последний карман) то negative
                    return $pocket->text;
                } else { //Среднее, positive и negative
                    return self::SinglePocket($behaviour_alias_1, $alias, $assessment).' '.$pocket->text;
                }
            }
        }
        throw new Exception("Карман не найден");
    }

    /**
     * Ищет текст по значению $behaviour_alias_2(негативное) + $behaviour_alias_2(негативное) / 2  для повидения $alias по карману
     * или для позитивноего и негативного
     * @param $behaviour_alias_1
     * @param $behaviour_alias_2
     * @param $behaviour_alias_3
     * @param $alias
     * @param $assessment
     * @return string
     * @throws Exception
     */
    public static function ThreePocketsWithTwoNegative($behaviour_alias_1, $behaviour_alias_2, $behaviour_alias_3,  $alias, $assessment) {

        $value_2 = self::getValueInAssessment($behaviour_alias_2, $assessment);
        $value_3 = self::getValueInAssessment($behaviour_alias_3, $assessment);
        $value_2 = $value_2 + $value_3;

        $pockets_2 = self::$pockets[$alias][$behaviour_alias_1];
        /* @var $pockets_2 ParagraphPocket[] */
        foreach($pockets_2 as $pocket_num => $pocket) {
            $left_direction = trim($pocket->left_direction);
            $right_direction = trim($pocket->right_direction);
            if(self::$left_direction($pocket->left, $value_2) && self::$right_direction($pocket->right, $value_2)){
                //Если человек мало сделал ошибок(первый карман) то получает positive
                if($pocket_num === 0) {
                    return self::SinglePocket($behaviour_alias_1, $alias, $assessment);
                } elseif($pocket_num === (count($pockets_2) - 1)) { //Если много(последний карман) то negative
                    return $pocket->text;
                } else { //Среднее, positive и negative
                    return self::SinglePocket($behaviour_alias_1, $alias, $assessment).' '.$pocket->text;
                }
            }
        }
        throw new Exception("Карман не найден");
    }

    /**
     * Берет значение оценки с попапа по alias
     * @param $value
     * @param $assessment
     * @return mixed
     */
    public static function getValueInAssessment($value, $assessment) {
        $array_parts = explode('[', $value);
        foreach($array_parts as $part) {
            if(!empty($part)){
                if(isset($assessment[rtrim($part, ']')])) {
                    $assessment = $assessment[rtrim($part, ']')];
                } else {
                    throw new AssessmentValueNotFound("Undefined index: ".rtrim($part, ']'));
                }
            }
        }
        return $assessment;
    }

    /**
     * Больше равно
     * @param $direction
     * @param $value
     * @return bool
     */
    public static function greater_equal($direction, $value) {
        return $direction <= $value;
    }

    /**
     * Меньше
     * @param $direction
     * @param $value
     * @return bool
     */
    public static function less($direction, $value) {
        return $direction > $value;
    }

    /**
     * Меньше равно
     * @param $direction
     * @param $value
     * @return bool
     */
    public static function less_equal($direction, $value) {
        return $direction >= $value;
    }
} 