<?php

class SimulationResultTextService {

    public static $pockets = [];

    public static $recommendations = [];

    public static function generate(Simulation $simulation) {
        foreach(ParagraphPocket::model()->findAll() as $pocket) {
            /* @var $pocket ParagraphPocket */
            self::$pockets[$pocket->paragraph_alias][$pocket->behaviour_alias][] = $pocket;
        }
        $assessment = json_decode($simulation->getAssessmentDetails(), true);
        /* @var $paragraphs Paragraph[] */
        $paragraphs = Paragraph::model()->findAll('scenario_id = '.$simulation->game_type->id.' order by order_number');
        $value = null;
        foreach($paragraphs as $paragraph) {
            switch($paragraph->method)
            {
                case 'SinglePocket':
                    self::$recommendations[] = self::SinglePocket($paragraph->value_1, $paragraph->alias, $assessment);
                    break;
                case 'TwoPocketsWithOneNegative':
                    self::$recommendations[] = self::TwoPocketsWithOneNegative($paragraph->value_1, $paragraph->value_2, $paragraph->alias, $assessment);
                    break;
                case 'TreePocketsWithTwoNegative':
                    self::$recommendations[] = self::TreePocketsWithTwoNegative();
                    break;
                default:
                    throw new Exception("Метод {$paragraph->method}");
                    break;
            }
        }

        return self::$recommendations;
    }

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

    public static function TwoPocketsWithOneNegative($behaviour_alias_1, $behaviour_alias_2, $alias, $assessment) {
        $value_2 = self::getValueInAssessment($behaviour_alias_2, $assessment);

        $pockets_2 = self::$pockets[$alias][$behaviour_alias_1];
        /* @var $pockets_2 ParagraphPocket[] */
        foreach($pockets_2 as $pocket_num => $pocket) {
            $left_direction = trim($pocket->left_direction);
            $right_direction = trim($pocket->right_direction);
            if(self::$left_direction($pocket->left, $value_2) && self::$right_direction($pocket->right, $value_2)){
                if($pocket_num === 0) {
                    return self::SinglePocket($behaviour_alias_1, $alias, $assessment);
                } elseif($pocket_num === (count($pockets_2) - 1)) {
                    return $pocket->text;
                } else {
                    return self::SinglePocket($behaviour_alias_1, $alias, $assessment).' '.$pocket->text;
                }
            }
        }
        throw new Exception("Карман не найден");
    }

    public static function TreePocketsWithTwoNegative() {
        return 'TreePocketsWithTwoNegative';
    }

    public static function getValueInAssessment($value, $assessment) {
        $array_parts = explode('[', $value);
        foreach($array_parts as $part) {
            if(!empty($part)){
                $assessment = $assessment[rtrim($part, ']')];
            }
        }
        return $assessment;
    }

    public static function greater_equal($direction, $value) {
        return $direction <= $value;
    }

    public static function less($direction, $value) {
        return $direction > $value;
    }

    public static function less_equal($direction, $value) {
        return $direction >= $value;
    }
} 