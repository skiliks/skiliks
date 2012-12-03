<?php



/**
 * Description of Profiler
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class Profiler
{
    private $start_time;

    private function get_time()
    {
            list($usec, $seconds) = explode(" ", microtime());
            return ((float)$usec + (float)$seconds);
    }

    public function startTimer()
    {
            $this->start_time = $this->get_time();
    }

    public function endTimer()
    {
            return ($this->get_time() - $this->start_time);
    }
}


