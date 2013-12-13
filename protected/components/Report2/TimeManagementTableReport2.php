<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gugu
 * Date: 11.03.13
 * Time: 17:42
 * To change this template use File | Settings | File Templates.
 */

use application\components\Logging\LogTable;

class TimeManagementTableReport2 extends LogTable {

    public function __construct($logs)
    {
        $this->logs = [];
        $persentsLabels = ['Время потраченное на задачи 1-го приоритета', 'Время потраченное на не важные задачи', 'Время ожидания или бездействия'];

        foreach ($logs as $log) {
            if ($log->getLabel() != 'Общая оценка эффективности использования времени') {

                if (in_array($log->getLabel() ,$persentsLabels)) {
                    $log->value = $log->value/100;
                }

                $this->logs[$this->k[$log->getLabel()]] = $log;


            }
        }

        ksort($this->logs);
    }

    public $labelsForPercents = [
        0, //'Продуктивное время (выполнение приоритетных задач)',
        1, //'Действия, не относящиеся к задачам 1 приоритета',
        2, //'Время ожидания'
    ];

    public $k = [
        'Время потраченное на задачи 1-го приоритета'    => 1,
        'Время потраченное на не важные задачи'          => 2,
        'Время ожидания или бездействия'                 => 3,
        'Время на которое задержался на на работе'       => 4,
        'Время потраченное на документы 1-го приоритета' => 5,
        'Время потраченное на встречи 1-го приоритета'   => 6,
        'Время потраченное на звонки 1-го приоритета'    => 7,
        'Время потраченное на письма 1-го приоритета'    => 8,
        'Время потраченное на планирование задачь 1-го приоритета' => 9,
        'Время потраченное на не важные документы'           => 10,
        'Время потраченное на не важные встречи'             => 11,
        'Время потраченное на не важные звонки'              => 12,
        'Время потраченное на не важные письма'              => 13,
        'Время потраченное на планирование не важных задачь' => 14,
    ];

    public $parameters = [
        'Время потраченное на задачи 1-го приоритета'    => 'Продуктивное время (выполнение приоритетных задач)',
        'Время потраченное на не важные задачи'          => 'Непродуктивное время (иные действия, не связанные с приоритетами)',
        'Время ожидания или бездействия'                 => 'Время ожидания и бездействия',
        'Время на которое задержался на на работе'       => 'Сверхурочное время',
        'Время потраченное на документы 1-го приоритета' => 'Работа с документами',
        'Время потраченное на встречи 1-го приоритета'   => 'Встречи',
        'Время потраченное на звонки 1-го приоритета'    => 'Звонки',
        'Время потраченное на письма 1-го приоритета'    => 'Работа с почтой',
        'Время потраченное на планирование задачь 1-го приоритета' => 'Планирование',
        'Время потраченное на не важные документы'           => 'Работа с документами',
        'Время потраченное на не важные встречи'             => 'Встречи',
        'Время потраченное на не важные звонки'              => 'Звонки',
        'Время потраченное на не важные письма'              => 'Работа с почтой',
        'Время потраченное на планирование не важных задачь' => 'Планирование',
    ];

    public $parameterGroups = [
        'Время потраченное на задачи 1-го приоритета'
            => '1. Распределение времени, %',

        'Время потраченное на не важные задачи'
            => '1. Распределение времени, %',

        'Время ожидания или бездействия'
            => '1. Распределение времени, %',

        'Время на которое задержался на на работе'
            => '2. Сверхурочное время (минуты)',

        'Время потраченное на документы 1-го приоритета'
            => '1.1 Продуктивное время (выполнение приоритетных задач, минуты))',

        'Время потраченное на встречи 1-го приоритета'
            => '1.1 Продуктивное время (выполнение приоритетных задач, минуты)',

        'Время потраченное на звонки 1-го приоритета'
            => '1.1 Продуктивное время (выполнение приоритетных задач, минуты)',

        'Время потраченное на письма 1-го приоритета'
            => '1.1 Продуктивное время (выполнение приоритетных задач, минуты)',

        'Время потраченное на планирование задачь 1-го приоритета'
            => '1.1 Продуктивное время (выполнение приоритетных задач, минуты)',

        'Время потраченное на не важные документы'
            => '1.2 Непродуктивное время (иные действия, не связанные с приоритетами)',

        'Время потраченное на не важные встречи'
            => '1.2 Непродуктивное время (иные действия, не связанные с приоритетами)',

        'Время потраченное на не важные звонки'
            => '1.2 Непродуктивное время (иные действия, не связанные с приоритетами)',

        'Время потраченное на не важные письма'
            => '1.2 Непродуктивное время (иные действия, не связанные с приоритетами)',

        'Время потраченное на планирование не важных задачь'
            => '1.2 Непродуктивное время (иные действия, не связанные с приоритетами)',
    ];

    public function getHeaders()
    {
        return [
            'Группа параметров',
            'Парамерт',
            "Эффективность\n использования\n времени, оценка",
        ];
    }

    public function getHeaderWidth() {
        return [
            24,
            24,
            14,
            55,
            45,
            14,
        ];
    }

    public function getId() {
        return 'time-management-table';
    }

    public function getTitle()
    {
        return 'Эффект. использования времени';
    }

    protected function getRow($item)
    {
        return [
            $this->parameterGroups[$item->getLabel()],
            $this->parameters[$item->getLabel()],
            $item->value,
            ];
    }

    /**
     * @param $point
     * @return string
     */
    public function getRowId($row)
    {
        return sprintf(
            'time-management-%s ',
            $row[3]
        );
    }

    public function getCellValueFormat($columnNo, $rowNo = null) {
        if (2 == $columnNo) {
            if (in_array($rowNo, $this->labelsForPercents)) {
                return \PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00;
            } else {
                return \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00;
            }
        } else {
            return \PHPExcel_Style_NumberFormat::FORMAT_TEXT;
        }
    }
}