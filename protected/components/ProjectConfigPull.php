<?php


class ProjectConfigPull {

    /**
     * @var array
     */
    private $configs = [];

    /**
     * @var array
     */
    private $simulationConfigs = [];

    /**
     * @var ProjectConfigPull
     */
    private static $_instance = null;

    /**
     *
     */
    private function __construct() {
        $configs = ProjectConfig::model()->findAll();

        /** @var ProjectConfig $config */
        foreach ($configs as $config) {
            $this->configs[$config->alias] = $config->getValue();

            if ($config->is_use_in_simulation) {
                $this->simulationConfigs[$config->alias] = $config->getValue();
            }
        }
    }

    /**
     * Просто оставим пустым
     */
    protected function __clone() {

    }

    /**
     * @return ProjectConfigPull
     */
    static public function getInstance() {
        if(is_null(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param String $alias
     *
     * @return mixed (bool/float/string)
     */
    public function get($alias) {
        if (false == isset($this->configs[[$alias]])) {
            return null;
        }

        return $this->configs[$alias];
    }

    /**
     * @return array
     */
    public function getAll() {
        return $this->configs;
    }

    /**
     * @return array
     */
    public function getAllSimConfigs() {
        return $this->configs;
    }
}