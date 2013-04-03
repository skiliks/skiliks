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
            $fullScenario = Scenario::model()->findByAttributes([
                'slug' => Scenario::TYPE_FULL
            ]);

            // events
            $this->assertNotNull(EventSample::model()->findByAttributes([
                'code'        => 'P5',
                'scenario_id' => $fullScenario->id,
            ])); 
            
            // CommunicationTheme
            $this->assertEquals(450, CommunicationTheme::model()->countByAttributes([
                'character_id' => null,
                'scenario_id' => $fullScenario->id,
            ]), 'Character');

            $this->assertEquals(67, CommunicationTheme::model()->countByAttributes([
                'phone' => 1,
                'scenario_id' => $fullScenario->id,
            ]), 'Phones');

            $this->assertEquals(11066, CommunicationTheme::model()->countByAttributes([
                'mail' => 1,
                'scenario_id' => $fullScenario->id,
            ]), 'Mail');

            $this->assertEquals(254, CommunicationTheme::model()->countByAttributes([
                'text' => '!проблема с сервером!',
                'scenario_id' => $fullScenario->id,
            ]));

            $this->assertEquals(217, CommunicationTheme::model()->countByAttributes([
                'mail_prefix' => 'fwdfwd',
                'scenario_id' => $fullScenario->id,
            ]), 'fwdfwd');

            $this->assertEquals(86, CommunicationTheme::model()->countByAttributes([
                'mail_prefix' => 'fwdrere',
                'scenario_id' => $fullScenario->id,
            ]), 'fwdrere');

            $this->assertEquals(86, CommunicationTheme::model()->countByAttributes([
                'mail_prefix' => 'fwdrerere',
                'scenario_id' => $fullScenario->id,
            ]), 'fwdrerere');

            $this->assertEquals(84, CommunicationTheme::model()->countByAttributes([
                'mail_prefix' => 'rererere',
                'scenario_id' => $fullScenario->id,
            ]), 'rererere');
            
            // Dialogs
            $this->assertEquals(
                Replica::model()->findByAttributes([
                    'code'             => 'E1',
                    'is_final_replica' => 1,
                    'scenario_id' => $fullScenario->id,
                    'excel_id'         => 12,
                ])->next_event_code, 
                'E1.2');

            $this->assertEquals(821, Replica::model()->count('scenario_id = '.$fullScenario->id));

            $this->assertNotNull(Replica::model()->findByAttributes([
                'code' => 'S12.3',
                'scenario_id' => $fullScenario->id,
            ]));

            $this->assertGreaterThan(0, FlagRunMail::model()->count('scenario_id = '.$fullScenario->id));

            $this->assertEquals(11, FlagBlockReplica::model()->count('scenario_id = '.$fullScenario->id), 'block replica');
            $this->assertEquals(10, FlagBlockDialog::model()->count('scenario_id = '.$fullScenario->id), 'block dialog');
            $this->assertEquals(21, Flag::model()->count('scenario_id = '.$fullScenario->id), 'flags');

            $this->assertEquals(5, FlagBlockMail::model()->count('scenario_id = '.$fullScenario->id), 'block mail');

            // end.
            $transaction->rollback();
        } catch (Exception $e) {
            $transaction->rollback();
            throw $e;
        }
    }
}
