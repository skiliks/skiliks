<?php
/**
 * YiiLessCClientScript class file.
 *
 * @author Devadatta Sahoo <devadatta.sahoo@nettantra.com>
 * @link http://www.nettantra.com/
 * @copyright Copyright &copy; 2012-2013 NetTantra Technologies (India) Private Limited
 * @license http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * YiiLessCClientScript manages CSS stylesheets for views with support for LESS.
 */

require_once(dirname(dirname(__FILE__))."/lib/lessphp/lessc.inc.php");

class YiiLessCClientScript extends CClientScript
{
    public $cache = true;

    public $basePath;

    public function registerLessFile($lessUrl, $cssUrl, $media = '')
    {
        $this->hasScripts = true;

        if (empty($this->basePath)) {
            $this->basePath = Yii::getPathOfAlias('webroot');
        }

        $lessFilePath = realpath($this->basePath . DIRECTORY_SEPARATOR . $lessUrl);
        $cssFilePath = str_replace('/', DIRECTORY_SEPARATOR, $this->basePath . DIRECTORY_SEPARATOR . $cssUrl);

        if (!file_exists(dirname($cssFilePath))) {
            mkdir(dirname($cssFilePath), 0777, true);
        }

        $lessCompiler = new lessc();

        if ($this->cache === false) {
            $lessCompiler->compileFile($lessFilePath, $cssFilePath);
        } else {
            $lessCompiler->checkedCompile($lessFilePath, $cssFilePath);
        }

        $this->cssFiles[$cssUrl] = $media;

        $params = func_get_args();
        $this->recordCachingAction('clientScript', 'registerLessFile', $params);

        return $this;
    }

    public function registerLess($id, $less, $media='')
    {
        $lessCompiler = new lessc();
        $css = $lessCompiler->compile($less, $id);
        return parent::registerCss($id, $css, $media);
    }
}

