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
    public function test_flags() {
        $import = new ImportGameDataService();
        $import->importFlags();
    }
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
            $import->importFlags();

            // events
            $this->assertNotNull(EventsSamples::model()->findByAttributes([
                'code' => 'P5'
            ])); 
            
            // CommunicationTheme
            $this->assertEquals(15, CommunicationTheme::model()->countByAttributes(['character_id' => null]), 'Character');
            $this->assertEquals(3, CommunicationTheme::model()->countByAttributes(['phone' => 1]), 'Phones');
            $this->assertEquals(95, CommunicationTheme::model()->countByAttributes(['mail' => 1]), 'Mail');
            $this->assertEquals(40, CommunicationTheme::model()->countByAttributes(['text' => '!проблема с сервером!']));
            $this->assertEquals(9, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'fwdfwd']), 'fwdfwd');
            $this->assertEquals(9, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'fwdrere']), 'fwdrere');
            $this->assertEquals(0, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'fwdrerere']), 'fwdrerere');
            $this->assertEquals(0, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'rererere']), 'rererere');
            
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

            $this->assertEquals(2, count(FlagBlockReplica::model()->findAll()), 'block replica');
            $this->assertEquals(4, count(Flag::model()->findAll()), 'flags');
            $this->assertEquals(0, count(FlagRunMail::model()->findAll()), 'run mail');

            // end.
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }
}
