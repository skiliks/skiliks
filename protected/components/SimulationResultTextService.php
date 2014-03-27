<?php

/**
 * Класс генерации Гороскопа
 */
class SimulationResultTextService {

    private static $sim_id = 0;
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

        self::$sim_id = $simulation->id;

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
                    case 'HugeProblemsPocketsConcatenation':
                        self::$recommendations[$paragraph->alias] = self::HugeProblemsPocketsConcatenation($paragraph->value_1, $paragraph->value_2, $paragraph->alias, $assessment);
                        break;
                    default:
                        throw new Exception("Метод {$paragraph->method} не найден");
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
    public static function SinglePocket($behaviour_alias_1, $alias, $assessment, $with_brackets=true) {
        $value_1 = self::getValueInAssessment($behaviour_alias_1, $assessment);
        if((int)$value_1 > 100 && $alias !== 'time.over_time') {

            echo "$alias -> $value_1 for sim_id ".self::$sim_id."\r\n";
            $value_1 = 100;
        }
        $pockets = self::$pockets[$alias][$behaviour_alias_1];
        /* @var $pockets ParagraphPocket[] */
        foreach($pockets as $pocket) {
            $left_direction = trim($pocket->left_direction);
            $right_direction = trim($pocket->right_direction);
            if(self::$left_direction($pocket->left, $value_1) && self::$right_direction($pocket->right, $value_1)) {

                return [
                        'text' => $pocket->text,
                        'short_text' => $with_brackets?'('.$pocket->short_text.')':$pocket->short_text,
                        'pocket' => [
                            'left' => $pocket->left,
                            'right' => $pocket->right
                        ]
                       ];
            }
        }

        throw new Exception("Карман не найден для $alias -> $value_1");

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

        $positive = self::SinglePocket($behaviour_alias_1, $alias, $assessment);
        $negative = self::SinglePocket($behaviour_alias_2, $alias, $assessment);

        return [
            'text' => $positive['text']." ".$negative['text'],
            'short_text' => $positive['short_text']
        ];
    }

    public static function HugeProblemsPocketsConcatenation($behaviour_alias_1, $behaviour_alias_2, $alias, $assessment){

        $positive = self::SinglePocket($behaviour_alias_1, $alias, $assessment, false);
        $negative = self::SinglePocket($behaviour_alias_2, $alias, $assessment, false);
        if((int)$negative['pocket']['left'] === 0) {
            return [
                'text' => $positive['text'],
                'short_text' => '('.$positive['short_text'].')'
            ];
        }else{
            return [
                'text' => $positive['text']." ".$negative['text'],
                'short_text' => '('.$positive['short_text'].', '.$negative['short_text'].')'
            ];
        }

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
                    throw new AssessmentValueNotFound("Undefined index: ".rtrim($part, ']').' on '.$value);
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
        return (int)$direction <= (int)$value;
    }

    /**
     * Меньше
     * @param $direction
     * @param $value
     * @return bool
     */
    public static function less($direction, $value) {
        return (int)$direction > (int)$value;
    }

    /**
     * Меньше равно
     * @param $direction
     * @param $value
     * @return bool
     */
    public static function less_equal($direction, $value) {
        return (int)$direction >= (int)$value;
    }

    public static function generateForAllFullCompleteSimulations() {
        ini_set('memory_limit', '-1');
        $scenario = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL]);
        /* @var Simulation[] $simulations */
        $simulations = Simulation::model()->findAll("scenario_id = :scenario_id and results_popup_cache is not null and end >= '2013-08-01 00:00:00'", [
            'scenario_id' => $scenario->id
        ]);
        //$count = count($simulations);
        //echo 'Найдено '.$count."\r\n";
        foreach($simulations as $simulation) {
            //echo 'Обработка sim_id = '.$simulation->id."\r\n";
            $simulation->popup_tests_cache = serialize([
                'popup' => SimulationResultTextService::generate($simulation, 'popup')
            ]);
            $simulation->save(false);
            //$count--;
            //echo 'Осталось '.$count."\r\n";
        }
    }
} 