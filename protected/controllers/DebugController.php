<?php

class DebugController extends SiteBaseController
{

    public function actionIndex()
    {
        $sim_id = Yii::app()->request->getParam('sim_id');
        $simulation = Simulation::model()->findByPk($sim_id);

        TimeManagementAggregatedDebug::model()->deleteAllByAttributes(['sim_id'=>$simulation->id]);

        $tma = new TimeManagementAnalyzerDebug($simulation);
        $tma->calculateAndSaveAssessments();
        $assessment_debug = TimeManagementAggregatedDebug::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'slug'=>'1st_priority_phone_calls'
        ]);

        $assessment = TimeManagementAggregated::model()->findByAttributes([
            'sim_id' => $simulation->id,
            'slug'=>'1st_priority_phone_calls'
        ]);

        echo 'sim_id = '.$simulation->id.' 1st_priority_phone_calls debug - '.$assessment_debug->value.' real - '.$assessment->value;
    }

    public function actionStyleCss()
    {
        $this->layout = false;
        $this->render('style_css');
    }

    public function actionStyleForPopupCss()
    {
        $this->layout = false;
        $this->render('style_for_popup_css');
    }

    public function actionStyleGrid()
    {
        $this->layout = false;
        $this->render('style_grid');
    }

    public function actionStyleGridResults()
    {
        $this->layout = false;
        $this->render('style_grid_results');
    }

    public function actionStyleBlocks()
    {
        $this->layout = false;
        $this->render('style_blocks');
    }

    public function actionStyleEmpty1280()
    {
        $this->layout = false;
        $this->render('style_empty_1280');
    }

    public function actionStyleEmpty1024()
    {
        $this->layout = false;
        $this->render('style_empty_1024');
    }

    public function actionXxx()
    {
//        $simulation = Simulation::model()->findByPk(900);
//
//        $ccb = new CheckConsolidatedBudget($simulation->id);
//        $ccb->calcPoints();
//        die;


//        $doc = new MyDocument();
//        $doc->fileName = 'Сводный бюджет_2014_план.xls';
//        $doc->sim_id = 714;
//        $doc->template_id = 20;
//        $doc->save(false);
//        $doc->refresh();
//
//        // var MyDocument $doc
//        $scData = $doc->getSheetList();
//
//        $filePath = tempnam('/tmp', 'excel_');
//
//        ScXlsConverter::sc2xls($scData, $filePath);
//
//        if (file_exists($filePath)) {
//            $xls = file_get_contents($filePath);
//        } else {
//            throw new Exception("Файл не найден");
//        }
//
//        $filename = $doc->sim_id . '_' . $doc->template->fileName;
//        header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
//        header('Content-Disposition: attachment; filename="' . $filename . '"');
//        echo $xls;

//        echo '<pre>';

        $scDoc = file_get_contents('http://loc.skiliks.com/6025_svodnyj_byudzhet_2014_plan');
        $scDoc = str_replace('СУММ', 'SUM', $scDoc);

        $excel = ScXlsConverter::sc2xls(json_decode($scDoc, true));

        PHPExcel_Calculation::getInstance()->clearCalculationCache();

        $worksheetNames = Yii::app()->params['analizer']['excel']['consolidatedBudget']['worksheetNames'];

        $whConsolidated = $excel->getSheetByName($worksheetNames['consolidated']);


//
//
//        $xlsFile =  new \PHPExcel();
//        $xlsFile->removeSheetByIndex(0);
//        $xlsFile->addSheet($whConsolidated);
//
//        $n10 = $whConsolidated->getCell('N10')->getCalculatedValue();
//        $n10 = $whConsolidated->getCell('N10')->getValue();


//        $whConsolidated->getCell('N10')->setValue('=SUM(продажи!B6:продажи!D6)');
//        // PHPExcel_Calculation::getInstance()->clearCalculationCache();
//
//        echo '</pre>';

//        $xlsFile =  new \PHPExcel();
//        $xlsFile->removeSheetByIndex(0);
//        $xlsFile->addSheet($whConsolidated);

//        $filePath = tempnam('/tmp', 'excel_');
//        ScXlsConverter::sc2xls(json_decode($scDoc, true), $filePath);
//
//        if (file_exists($filePath)) {
//            $xls = file_get_contents($filePath);
//        } else {
//            throw new Exception("Файл не найден");
//        }
//
//        $filename = 'D1.xlsx';
//        header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
//        header('Content-Disposition: attachment; filename="' . $filename . '"');
//        echo $xls;
    }
    // PAYMENT CONTROLLER METHODS

    public function actionDoCashPayment() {

        /** @var YumUser $user */
        $user = Yii::app()->user->data();

        if (!Yii::app()->request->getIsAjaxRequest() || !$user->isAuth() || !$user->isCorporate()) {
            echo 'false';
            Yii::app()->end();
        }

        $account = $user->account_corporate;


        $paymentMethod = new CashPaymentMethod();

        $account->inn                 = $paymentMethod->inn     = Yii::app()->request->getParam('inn');
        $account->cpp                 = $paymentMethod->cpp     = Yii::app()->request->getParam('cpp');
        $account->bank_account_number = $paymentMethod->account = Yii::app()->request->getParam('account');
        $account->bic                 = $paymentMethod->bic     = Yii::app()->request->getParam('bic');

        $errors = CActiveForm::validate($paymentMethod);

        if ($errors) {
            echo $errors;
        } elseif (!$account->hasErrors()) {
            $account->save();

            echo sprintf(
                Yii::t('site', 'Thanks for your order, Invoice was sent to %s. Plan will be available upon receipt of payment'),
                $user->profile->email
            );
        }
    }

    public function actionYyy()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $scenario = Scenario::model()->findByAttributes(['slug' => Scenario::TYPE_LITE]);

        $i = new Invite();
        $i->scenario = $scenario;
        $i->scenario_id = $scenario->id;
        $i->is_display_simulation_results = 1;
        $i->save(false);

        $this->redirect('/simulation/promo/lite/'.$i->id);
    }
}

