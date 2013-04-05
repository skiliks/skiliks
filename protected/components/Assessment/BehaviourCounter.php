<?php

/**
 * @author slavka
 */
class BehaviourCounter 
{
    /**
     * Counter of "1" and "0"
     * @var integer
     */
    public $total = 0;
    
    /**
     * Counter of "1"
     * @var integer
     */
    public $score = 0;
    
    /**
     * @var HeroBehaviour
     */
    public $mark;
    
    /**
     * @param float $addValue
     */
    public function update($addValue) {
        $this->total++;
        $this->score += $addValue;
    }
    
    /**
     * @return float || null
     */
    public function getValue()
    {
        if (null === $this->mark) {
            return null;
        } else {
            switch ($this->mark->type_scale) {
                case 1: // positive
                    return $this->getValuePositive();
                    break;
                case 2: // negative
                    return -$this->score*$this->mark->scale;
                    break;
                case 3: // personal
                    return $this->score;
                    break;                    
                default:
                    return null;
                    break;                    
            }
        }
    }
    
    /**
     * @return float || null
     */
    private function getValuePositive()
    {
        if (0 == $this->total) {
            $total = 1;
        } else {
            $total = $this->total;
        }

        return ($this->score/$total)*$this->mark->scale;
    }
}

