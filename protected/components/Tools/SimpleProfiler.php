<?php

/**
 *
 * @author slavka
 */
class SimpleProfiler
{
    public $isEnabled = false;
    
    public $timers = [];

    /**
     *
     * @param bool $isEnabled
     */
    public function __construct($isEnabled = false)
    {
        $this->isEnabled = $isEnabled;
    }

    /**
     * 
     */
    public function enable()
    {
        $this->isEnabled = true;
    }
    
    /**
     * 
     */
    public function disable()
    {
        $this->isEnabled = false;
    }
    
    /**
     * 
     * @param type $name
     */
    public function startTimer($name = 'default') 
    {
        $this->timers[$name]['start'] = microtime(true);
    }
    
    /**
     * 
     * @param type $name
     * @return type
     */
    public function getTimerDiff($name = 'default') 
    {
        return microtime(true) - $this->timers[$name]['start'];
    }
    
    /**
     * 
     * @param type $name
     * @return type
     */
    public function getTimerDiffAndReset($name = 'default') 
    {
        $result = $this->getTimerDiff($name);
        $this->startTimer($name);
        
        return number_format($result, 10);
    }
    
    /**
     * 
     * @param type $name
     */
    public function removeTimer($name = 'default') 
    {
        unset($this->timers[$name]);
    }
    
    public function render($beforeText, $method = 'getTimerDiffAndReset', $name = 'default', $afterText = "\n") {
        if ($this->isEnabled) {
            echo $beforeText.$this->{$method}($name).$afterText;
        }
    }
}

