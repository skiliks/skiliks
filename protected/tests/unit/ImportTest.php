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
    public function testFullImport()
    {
        $transaction = Yii::app()->db->beginTransaction();
        try {
            //(new ImportGameDataService())->importWithoutTransaction();

            // events
            $this->assertNotNull(EventSample::model()->findByAttributes([
                'code' => 'P5'
            ])); 
            
            // CommunicationTheme
            $this->assertEquals(770, CommunicationTheme::model()->countByAttributes(['character_id' => null]), 'Character');
            $this->assertEquals(134, CommunicationTheme::model()->countByAttributes(['phone' => 1]), 'Phones');
            $this->assertEquals(22002, CommunicationTheme::model()->countByAttributes(['mail' => 1]), 'Mail');
            $this->assertEquals(254, CommunicationTheme::model()->countByAttributes(['text' => '!проблема с сервером!']));
            $this->assertEquals(434, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'fwdfwd']), 'fwdfwd');
            $this->assertEquals(172, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'fwdrere']), 'fwdrere');
            $this->assertEquals(172, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'fwdrerere']), 'fwdrerere');
            $this->assertEquals(166, CommunicationTheme::model()->countByAttributes(['mail_prefix' => 'rererere']), 'rererere');
            
            // Dialogs
            $this->assertEquals(
                Replica::model()->findByAttributes([
                    'code'             => 'E1',
                    'is_final_replica' => 1,
                    'excel_id'         => 12
                ])->next_event_code, 
                'E1.2');
            $this->assertEquals(1641, Replica::model()->count());
            $this->assertNotNull(Replica::model()->findByAttributes(['code' => 'S12.3']));

            $this->assertGreaterThan(0, FlagRunMail::model()->count());

            $this->assertEquals(11, FlagBlockReplica::model()->count(), 'block replica');
            $this->assertEquals(10, FlagBlockDialog::model()->count(), 'block replica');
            $this->assertEquals(21, Flag::model()->count(), 'flags');

            $this->assertEquals(10, FlagBlockMail::model()->count(), 'block mail');

            // end.
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }
}
