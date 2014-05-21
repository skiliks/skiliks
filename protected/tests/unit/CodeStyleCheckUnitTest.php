<?php
class CodeStyleCheckUnitTest  extends CDbTestCase
{
    use UnitTestBaseTrait;

    /**
     *
     */
    public function testVarDumpsAndScriptStoppres()
    {
        $folders = [
            /*'/components/',
            '/models/',
            '/controllers/',
            '/modules/',
            '/views/',*/
            '/assets/js/game/',
            '/assets/js/game/models/',
            '/assets/js/game/views/',
            '/assets/js/game/views/mail/',
            '/assets/js/game/models/window/',
        ];
        $targets = ['var_dump', 'die(', 'exit(', '====', '>>>', '<<<', '<? ', '</br>', 'debugger'];

        foreach ($folders as $folder) {
            $dir = Yii::app()->getBasePath().$folder;
            $filesList = [];
            getFilesList($dir, $filesList);

            echo "$dir ".count($filesList)." \n";

            foreach ($filesList as $filename) {
                $fileText = file_get_contents($filename);
                echo $filename." \n";

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

            // exceptions
            getFilesList($dir, $filesList, [
                'yiic.php',
                'ImportGameDataService.php',
                'SimulationBaseController.php',
                'SiteBaseController.php',
                'tcpdf.php',
                'tcpdf_barcodes_1d.php',
                'tcpdf_barcodes_2d.php',
                'tcpdf_import.php',
                'AnalyticalFileGenerator.php',
                'SimulationResultTextService.php',
                'CheckAssessmentResults.php'
            ]);

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

/**
 * @param string $dir
 * @param array $results
 * @param array $excludeFiles
 * @param array $filesToIgnore
 * @param array $extensionsToUse
 * @param array $excludeFolders
 */
function getFilesList(
    $dir,
    &$results,
    $excludeFiles = ['yiic.php'],
    $filesToIgnore = ['.', '..', 'lib', 'pdf.js'],
    $extensionsToUse = ['php','js'],
    $excludeFolders = ['.', '..', 'yii-sentry-log'] )
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