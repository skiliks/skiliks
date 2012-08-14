<?php


/**
 * Модель задачи
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Task {
    
    public $id;
    
    public $title;
    
    public $startTime = 0;
    
    public $duration = 60;
    
    public $type = 1;
    
    public $simulation;
}

?>
