<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 29.01.13
 * Time: 22:58
 * To change this template use File | Settings | File Templates.
 */
class ImportTest extends CDbTestCase
{
    public function test_Full_Import()
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $import = new ImportGameDataService();
            $import->importCharacters();
            $import->importCharactersPointsTitles();
            $import->importLearningGoals();
            $import->importDialogReplicas();
            $import->importEmailSubjects();
            $this->assertEquals(CommunicationTheme::model()->countByAttributes(['character_id' => null]), 41);
            $this->assertEquals(CommunicationTheme::model()->countByAttributes(['phone' => 1]), 67);
            $this->assertEquals(CommunicationTheme::model()->countByAttributes(['mail' => 1]), 4082);
            $import->importEmails();
            $import->importMailTasks();
            $import->importEventSamples();
            $import->importTasks();
            $import->importMyDocuments();
            $import->importActivity();
            $this->assertEquals(CommunicationTheme::model()->countByAttributes(['text' => '!проблема с сервером!']),43);
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function test_events_samples() {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $import = new ImportGameDataService();
            $import->importEventSamples();
            $this->assertNotNull(EventsSamples::model()->findByAttributes([
                'code' => 'P5'
            ]));
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function test_subject_import() {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $import = new ImportGameDataService();
            $import->importEmailSubjects();
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }

    public function test_dialog_import() {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $import = new ImportGameDataService();
            $import->importDialogReplicas();
            $this->assertEquals(Dialogs::model()->findByAttributes([
                'code' => 'E1',
                'is_final_replica' => 1,
                'excel_id' => 12
            ])->next_event_code, 'E1.2');
            $this->assertEquals(Dialogs::model()->count(), 821);
            $this->assertNotNull(Dialogs::model()->findByAttributes(['code' => 'S12.3']));
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }
}
