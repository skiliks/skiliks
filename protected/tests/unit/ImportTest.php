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
            $import->setFilename('forUnitTests.xlsx');
            $import->importCharacters();
            $import->importCharactersPointsTitles();
            $import->importLearningGoals();
            $import->importDialogReplicas();
            $import->importEmailSubjects();
            $import->importEmails();
            $import->importMailTasks();
            $import->importEventSamples();
            $import->importTasks();
            $import->importMyDocuments();
            $import->importActivity();
            
            // events
            $this->assertNotNull(EventsSamples::model()->findByAttributes([
                'code' => 'P5'
            ])); 
            
            // CommunicationTheme
            $this->assertEquals(2, CommunicationTheme::model()->countByAttributes(['character_id' => null]));
            $this->assertEquals(2, CommunicationTheme::model()->countByAttributes(['phone' => 1]));
            $this->assertEquals(63, CommunicationTheme::model()->countByAttributes(['mail' => 1]));
            $this->assertEquals(36, CommunicationTheme::model()->countByAttributes(['text' => '!проблема с сервером!']));
            
            // Dialogs
            $this->assertEquals(
                Dialogs::model()->findByAttributes([
                    'code'             => 'E1',
                    'is_final_replica' => 1,
                    'excel_id'         => 12
                ])->next_event_code, 
                'E1.2');
            $this->assertEquals(19, Dialogs::model()->count());
            $this->assertNotNull(Dialogs::model()->findByAttributes(['code' => 'S12.3']));
            
            // end.
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }
}
