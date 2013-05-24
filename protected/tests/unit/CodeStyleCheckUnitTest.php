<?php
class CodeStyleCheckUnitTest  extends CDbTestCase
{
    /**
     *
     */
    public function testVarDumpsAndScriptStoppres()
    {
        $folders = ['/components/', '/models/', '/controllers/', 'modules'];
        $targets = ['var_dump', 'die(', 'exit('];

        foreach ($folders as $folder) {
            $dir = Yii::app()->getBasePath().$folder;
            $filesList = [];
            getFilesList($dir, $filesList);

            foreach ($filesList as $filename) {
                $fileText = file_get_contents($filename);

                foreach ($targets as $target) {
                    $this->assertEquals(
                        0,
                        substr_count($fileText, $target),
                        $target.' in "'.$filename.'" !'
                    );
                }
            }
        }
    }

    /**
     *
     */
    public function testEchos()
    {
        $folders = ['/components/', '/models/', 'modules'];
        $targets = ['echo', 'print(', 'print_r('];

        foreach ($folders as $folder) {
            $dir = Yii::app()->getBasePath().$folder;
            $filesList = [];
            getFilesList($dir, $filesList, ['yiic.php', 'ImportGameDataService.php', 'AjaxController.php']);

            foreach ($filesList as $filename) {
                $fileText = file_get_contents($filename);

                foreach ($targets as $target) {
                    $this->assertEquals(
                        0,
                        substr_count($fileText, $target),
                        $target.' in "'.$filename.'" !'
                    );
                }
            }
        }
    }
}

function getFilesList($dir, &$results, $excludeFiles = ['yiic.php'],
    $filesToIgnore = ['.', '..', 'lib'], $extensionsToUse = ['php'],
    $excludeFolders = ['yii-sentry-log'] )
{
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($filename = readdir($dh)) !== false) {
                if (is_dir($dir.$filename)) {
                    if (false === in_array($filename, $excludeFolders)) {
                        getFilesList($dir.$filename, $results, $excludeFiles, $filesToIgnore, $extensionsToUse, $excludeFolders);
                    }
                } else {
                    $file_parts = pathinfo($filename);
                    if (false === in_array($filename, $filesToIgnore) &&
                        isset($file_parts['extension']) &&
                        in_array($file_parts['extension'], $extensionsToUse) &&
                        false === in_array($filename, $excludeFiles)) {
                            $results[] = str_replace(['//', '/./'], ['/', '/'], $dir.'/'.$filename);
                    }
                }
            }
            closedir($dh);
        }
    }
}