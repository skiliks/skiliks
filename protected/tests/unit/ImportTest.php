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
    /**
     * Проверяет результаты импорта:
     * 1. Создание событий для планировщика
     * 2. Создание тем гибких комуникаций: origin, re ,fwd, fwdfwd, fwdrere, fwdrerere, rererere
     * 3. Создание диалогов
     * 4. Создание флагов
     * 5. Создание правил для флагов: блокировка диалога, блокировка реплики, блокировка письма, инициализация отправки письма
     */
    public function test_Full_Import()
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            $import = new ImportGameDataService();
            $import->setFilename('forUnitTests.xlsx');
            $import->importCharacters();
            $import->importHeroBehaviours();
            $import->importLearningGoals();
            $import->importDialogReplicas();
            $import->importEmailSubjects();
            $import->importEmails();
            $import->importMailEvents();
            $import->importMailTasks();
            $import->importEventSamples();
            $import->importTasks();
            $import->importMyDocuments();
            $import->importActivity();
            $import->importFlags();
            $import->importFlagsRules();

            // events
            $this->assertNotNull(EventSample::model()->findByAttributes([
                'code' => 'P5'
            ])); 
            
            // CommunicationTheme
            $this->assertEquals(47, CommunicationTheme::model()->countByAttributes(['character_id' => null]), 'Character');
            $this->assertEquals(3, CommunicationTheme::model()->countByAttributes(['phone' => 1]), 'Phones');
            $this->assertEquals(292, CommunicationTheme::model()->countByAttributes(['mail' => 1]), 'Mail');
            $this->assertEquals(87, CommunicationTheme::model()->countByAttributes(['text' => '!проблема с сервером!']));
            $this->assertEquals(20, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'fwdfwd']), 'fwdfwd');
            $this->assertEquals(38, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'fwdrere']), 'fwdrere');
            $this->assertEquals(19, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'fwdrerere']), 'fwdrerere');
            $this->assertEquals(9, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'rererere']), 'rererere');
            
            // Dialogs
            $this->assertEquals(
                Replica::model()->findByAttributes([
                    'code'             => 'E1',
                    'is_final_replica' => 1,
                    'excel_id'         => 12
                ])->next_event_code, 
                'E1.2');
            $this->assertEquals(19, Replica::model()->count());
            $this->assertNotNull(Replica::model()->findByAttributes(['code' => 'S12.3']));

            $this->assertEquals(0, FlagBlockReplica::model()->count(), 'block replica');
            $this->assertEquals(6, FlagBlockDialog::model()->count(), 'block replica');
            $this->assertEquals(4, Flag::model()->count(), 'flags');
            $this->assertEquals(0, FlagRunMail::model()->count(), 'run mail');
            $this->assertEquals(1, FlagBlockMail::model()->count(), 'block mail');

            // end.
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }
}
