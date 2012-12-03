<?php


/**
 * Модель задачи
 *
 * @todo: move to models
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


