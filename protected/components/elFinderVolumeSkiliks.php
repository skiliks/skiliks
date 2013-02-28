<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 15.01.13
 * Time: 16:26
 * To change this template use File | Settings | File Templates.
 */
class elFinderVolumeSkiliks extends elFinderVolumeLocalFileSystem
{
    protected $driverId = 's';
    public $sim_id;
    /**
     * Return files list in directory.
     *
     * @param  string  $path  dir path
     * @return array
     * @author Dmitry (dio) Levashov
     **/
    protected function _scandir($path) {
        $files = array();

        foreach (MyDocument::model()->findAllByAttributes(['sim_id' => $this->options['sim_id'], 'hidden' => 0]) as $file) {
                $files[] = $path.DIRECTORY_SEPARATOR.$file->template->srcFile;
        }
        return $files;
    }

    protected function _stat($path) {
        $stat = array();

        if (!file_exists($path)) {
            return $stat;
        }

        if ($path != $this->root && is_link($path)) {
            if (($target = $this->readlink($path)) == false
                || $target == $path) {
                $stat['mime']  = 'symlink-broken';
                $stat['read']  = false;
                $stat['write'] = false;
                $stat['size']  = 0;
                return $stat;
            }
            $stat['alias']  = $this->_path($target);
            $stat['target'] = $target;
            $path  = $target;
            $lstat = lstat($path);
            $size  = $lstat['size'];
        } else {
            $size = @filesize($path);
        }

        $dir = is_dir($path);

        $stat['mime']  = $dir ? 'directory' : $this->mimetype($path);
        $stat['ts']    = filemtime($path);
        $stat['read']  = is_readable($path);
        $stat['write'] = is_writable($path);
        if ($stat['read']) {
            $stat['size'] = $dir ? 0 : $size;
        }

        return $stat;
    }

}
