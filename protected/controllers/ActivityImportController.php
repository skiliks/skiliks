<?php
require_once(__DIR__ . '/../extensions/PHPExcel.php');

class ActivityImportController extends AjaxController
{
    private $activity_types = array(
    'Documents_leg' => 'document_id',
    'In_dial_reg' => 'dialog_id',
    'Out_dial_leg' => 'dialog_id',

    );

    public function actionIndex()
    {
        $fileName = __DIR__ . '/../../media/xls/activity.xlsx';
        $cache_method = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
        PHPExcel_Settings::setCacheStorageMethod($cache_method);

        $reader = PHPExcel_IOFactory::createReader('Excel2007');

        $worksheet_names = $reader->listWorksheetNames($fileName);
        $reader->setLoadSheetsOnly(array(
            'Leg_actions',
            'Tasks'));
        $excel = $reader->load($fileName);
        $sheet = $excel->getSheetByName('Leg_actions');
        $columns = array();
        for ($i = 0; ; $i++) {
            $row_title = $sheet->getCellByColumnAndRow($i, 1)->getValue();
            if ($row_title) {
                $columns[$row_title] = $i;
            } else {
                break;
            }
        }
        for ($i = $sheet->getRowIterator(2); $i->valid(); $i->next()) {
            $cell = $sheet->getCellByColumnAndRow($columns['Task code'], $i->key());
            $activity = Activity::model()->findByPk($cell->getValue());
            if ($activity === null) {
                $activity = new Activity();
                $activity->id=$cell->getValue();
            }
            $activity->parent = $sheet->getCellByColumnAndRow($columns['Parent'], $i->key())->getValue();
            $activity->grandparent = $sheet->getCellByColumnAndRow($columns['Grand parent'], $i->key())->getValue();
            $activity->name = $sheet->getCellByColumnAndRow($columns['Task name'], $i->key())->getValue();
            $category = $sheet->getCellByColumnAndRow($columns['Категория'], $i->key())->getCalculatedValue();
            $activity->category_id = ($category === '-' ? null : $category);
            if (!$activity->validate()) {
                $this->render('index', array('errors' => $activity->getErrors()));
                return;
            }
            $activity->save();
            $activityAction = new ActivityAction();
            $activityAction->activity_id = $activity->id;
            #$activityAction->
        }
        $this->render('index', array());
    }

    // Uncomment the following methods and override them if needed
    /*
    public function filters()
    {
        // return the filter configuration for this controller, e.g.:
        return array(
            'inlineFilterName',
            array(
                'class'=>'path.to.FilterClass',
                'propertyName'=>'propertyValue',
            ),
        );
    }

    public function actions()
    {
        // return external action classes, e.g.:
        return array(
            'action1'=>'path.to.ActionClass',
            'action2'=>array(
                'class'=>'path.to.AnotherActionClass',
                'propertyName'=>'propertyValue',
            ),
        );
    }
    */
}