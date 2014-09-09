<?php

class CorporateAccountExport {

    /**
     * @var array
     */
    public $sheets = [];

    /**
     * @var int
     */
    public $sheet_number = 0;

    /**
     * @var int
     */
    public $column_number = 0;

    /**
     * @var int
     */
    public $row_number = 0;

    /**
     * @var PHPExcel
     */
    public $document;

    public $info_name = '';

    public $info_company_name = '';

    public $info_simulation_id;


    public function addColumn($text, $width = null) {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];
        $sheet->setCellValueByColumnAndRow($this->column_number, $this->row_number, $text);
        $sheet->getStyleByColumnAndRow($this->column_number, $this->row_number)
            ->getBorders()->getOutline()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        if($width !== null){
            $sheet->getColumnDimensionByColumn($this->column_number)->setWidth($width);
            $sheet->getStyleByColumnAndRow($this->column_number, $sheet->getHighestRow())
                ->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $this->column_number++;
        return $sheet;
    }


    public function addColumnRight($text, $width = null) {
        $text = str_replace('.', ',', $text);
        $sheet = $this->addColumn($text, $width);
        $sheet->getStyleByColumnAndRow($this->column_number-1, $sheet->getHighestRow())->getFill()
            ->applyFromArray(array('type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'startcolor' => array('rgb' => 'FFFF99')
            ));
        $sheet->getStyleByColumnAndRow($this->column_number-1, $sheet->getHighestRow())
            ->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
    }


    public function addRow(){
        $this->row_number++;
        $this->column_number = 0;
    }

    public function createDocument() {
        $this->document =  new PHPExcel();
        $this->document->removeSheetByIndex(0);
    }

    public function addSheet($name) {
        /* @var $this->document PHPExcel */
        $sheet = new PHPExcel_Worksheet($this->document, $name);
        $this->document->addSheet($sheet);
        $this->sheet_number++;
        $this->sheets[$this->sheet_number] = $sheet;
        $this->row_number = 0;
        $this->column_number = 0;
    }

    public function setBorderBold() {
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];
        for ($i = 0; $i < $this->column_number; $i++) {
            $sheet->getStyleByColumnAndRow($i, $this->row_number)
                ->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
        }
        $sheet->getStyleByColumnAndRow($this->column_number-1, $sheet->getHighestRow())
            ->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
    }

    public function setBoldFirstRow(){
        /* @var $sheet PHPExcel_Worksheet */
        $sheet = $this->sheets[$this->sheet_number];
        $sheet->getStyle('A1:Z1')->applyFromArray(['font' => ['bold' => true]]);
    }

    public function save() {

        $doc = new \PHPExcel_Writer_Excel2007($this->document);
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"Corporate_accounts.xlsx\"");
        $doc->save('php://output');

    }

    /**
     * @param YumUser $user
     */
    public function export($user) {
        /* @var $accounts UserAccountCorporate[] */
        if (null == $user->emails_white_list) {
            $accounts = UserAccountCorporate::model()->findAll();
        } else {
            $emails = explode(
                ',',
                str_replace(' ', '', $user->emails_white_list)
            );

            $accounts =UserAccountCorporate::model()
                ->with('user', 'user.profile')
                ->findAll([
                    'condition' => sprintf(" profile.email IN ('%s') ", implode("','", $emails)),
                    "order"     => ' t.user_id DESC ',
                ]);
        }

        $this->createDocument();
        $this->addSheet("Все");
        $this->addRow();
        $this->addColumn('ID юзера');
        $this->addColumn('Имя', 25);
        $this->addColumn('Фамилия', 25);
        $this->addColumn('Статус пользователя ( status_for_sales)', 25);
        $this->addColumn('Email', 25);
        $this->addColumn('Домен', 25);
        $this->addColumn('Название компании', 25);
        $this->addColumn('Отрасль', 25);
        $this->addColumn('Описание от пользователя (description)', 25);
        $this->addColumn('Описание от скиликса (description_for_sales)', 25);
        $this->addColumn('Количество приглашений в аккаунте', 25);
        $this->addColumn('Дата регистрации', 25);
        $this->addColumn('Кол-во проплат', 25);
        $this->addColumn('Кол-во отправленных приглашений', 30);
        $this->addColumn('Кол-во пройденных симуляций сам-себе', 30);
        $this->addColumnRight('Кол-во пройденных симуляций по приглашениям', 30);
        $this->setBoldFirstRow();
        $this->setBorderBold();

        foreach($accounts as $account) {
            $this->addRow();
            $this->addColumn($account->user->id);
            $this->addColumn($account->user->profile->firstname);
            $this->addColumn($account->user->profile->lastname);
            $this->addColumn($account->getStatusForSales());
            $this->addColumn($account->user->profile->email);
            $this->addColumn($account->site);
            $this->addColumn($account->company_name_for_sales);
            $this->addColumn($account->industry_for_sales);
            $this->addColumn($account->company_description);
            $this->addColumn($account->description_for_sales);
            $this->addColumn($account->invites_limit);
            $this->addColumn(date('d.m.Y', $account->user->createtime));
            $this->addColumn($account->getNumberOfPaidOrders());
            $this->addColumn($account->getNumberOfInvitationsSent());
            $this->addColumn($account->getNumberOfFullSimulationsForSelf());
            $this->addColumnRight($account->getNumberOfFullSimulationsForPeople());
        }
        $this->setBorderBold();
        $this->save();
    }

} 