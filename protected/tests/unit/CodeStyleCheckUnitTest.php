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

//    public function testPDF()
//    {
//        $simulation = Simulation::model()->findByPk(15721);
//
//        for ($i = 0; $i < 34; $i++) {
//            $assessmentVersion = $simulation->assessment_version;
//            $data = json_decode($simulation->getAssessmentDetails(), true);
//
//            if (null == $simulation->popup_tests_cache) {
//                $simulation->popup_tests_cache = serialize([
//                    'popup' => SimulationResultTextService::generate($simulation, 'popup')
//                ]);
//                $simulation->save(false);
//            }
//
//            $pdf = new AssessmentPDF();
//            $username = $simulation->user->profile->firstname.' '.$simulation->user->profile->lastname;
//
//            $pdf->setImagesDir('simulation_details_'.$assessmentVersion.'/images/');
//
//            $pdf->pdf->setCellHeightRatio(1);
//
//            // 1. Спидометры и прочее
//            $pdf->addPage(1);
//
//            $data['performance']['total'] = $i*3;
//            $data['management']['total'] = $i*3+1;
//            $data['time']['total'] = $i*3+2;
//
//            $pdf->writeTextBold($username, 3.5, 3.5, 21);
//            $pdf->addRatingPercentile(94, 38.0, $data['percentile']['total']);
//            $pdf->addRatingOverall(86.6, 48.8, $data['overall']);
//            $pdf->addSpeedometer(21, 109.7, $data['time']['total']);
//            $pdf->addSpeedometer(89, 109.7, $data['performance']['total']);
//            $pdf->addSpeedometer(158, 109.7, $data['management']['total']);
//
//            $filename = $this->createFilename($simulation, 'results');
//
//            $path = __DIR__.'/../../system_data/simulation_details/'.$i;
//
//            $pdf->saveOnDisk($path.'_'.$filename, false);
//        }
//    }
//
//    private function createFilename(Simulation $simulation, $type) {
//
//        $filename = '';
//        $filename .= StringTools::CyToEnWithUppercase($simulation->user->profile->firstname);
//        $filename .= '_'.StringTools::CyToEnWithUppercase($simulation->user->profile->lastname);
//        if($simulation->invite->vacancy !== null) {
//            $filename .= '_'.preg_replace("/[^a-zA-Z0-9]/", "", StringTools::CyToEnWithUppercase($simulation->invite->vacancy->label));
//        }
//
//        $filename .= '_'.$type.'_'.date('dmy', strtotime($simulation->end));
//
//        return str_replace(' ', '_', $filename);
//    }
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