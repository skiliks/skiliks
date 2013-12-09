<?php

class PDFController extends SiteBaseController {

    public function actionSimulationDetailPDF()
    {

        $this->user = Yii::app()->user->data();
        if(null === $this->user && false === $this->user->isAuth()) {
            $this->redirect('/registration');
        }

        if(false === $this->user->isCorporate()) {
            $this->redirect('/dashboard');
        }

        $sim_id = $this->getParam('sim_id');
        /* @var $simulation Simulation */
        $simulation = Simulation::model()->findByPk($sim_id);
        $isUser = $simulation->user_id === $this->user->id;
        $isOwner = $simulation->invite->owner_id === $this->user->id;
        $isAdmin = $this->user->isAdmin();
        if(false === $isUser || false === $isOwner || false === $isAdmin) {
            $this->redirect('/dashboard');
        }

        $data = json_decode($simulation->getAssessmentDetails(), true);

        $pdf = new AssessmentPDF();
        $username = $this->user->profile->firstname.' '.$this->user->profile->lastname;
        $pdf->addPage();
        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->addRatingPercentile(92.4, 37.6, $data['percentile']['total']);
        $pdf->addRatingOverall(85, 48, $data['overall']);
        $pdf->addSpeedometer(19.8, 109.2, $data['time']['total']);
        $pdf->addSpeedometer(87.9, 109.2, $data['performance']['total']);
        $pdf->addSpeedometer(156.9, 109.2, $data['management']['total']);

        $pdf->addPage();
        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->writeTextBold('100%', 184.1, 28.4, 10, array(255,255,255));
        $pdf->addTimeDistribution(53.9, 89.7,    25,25,50);
        $pdf->addOvertime(156.2, 90.7,  50, 25, 25,  120);

        $pdf->writeTextBold('100%', 177, 175.84, 10, [255,255,255]);//Распеределение времени

        $pdf->writeTextBold('87%', 82.1, 197.5, 16);//Продуктивное время


        $pdf->writeTextBold('100%', 185, 197.5, 16);//Не продуктивное время
        //Positive
        $x_positive = 33;
        $pdf->addTimeBarProductive($x_positive, 218, 22, 100); //Документы

        $pdf->addTimeBarProductive($x_positive, 228.5, 60, 100);//Встречи

        $pdf->addTimeBarProductive($x_positive, 239, 38, 100);//Звонки

        $pdf->addTimeBarProductive($x_positive, 249.5, 87, 100);//Почта

        $pdf->addTimeBarProductive($x_positive, 260, 4, 100);//План

        //Negative
        $y_positive = 137;
        $pdf->addTimeBarUnproductive($y_positive, 218, 49, 100); //Документы

        $pdf->addTimeBarUnproductive($y_positive, 228.5, 34, 100);//Встречи

        $pdf->addTimeBarUnproductive($y_positive, 239, 100, 100);//Звонки

        $pdf->addTimeBarUnproductive($y_positive, 249.5, 87, 100);//Почта

        $pdf->addTimeBarUnproductive($y_positive, 260, 4, 100);//План

        $pdf->addPage();
        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->writeTextBold('100%', 134, 27.8, 10, [255,255,255]);//Результативность

        $pdf->addUniversalBar(77, 45.8, 23, 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//Срочно
        $pdf->addUniversalBar(77, 56.3, 98, 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//Высокий приоритет
        $pdf->addUniversalBar(77, 66.9, 43, 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//Средний приоритет
        $pdf->addUniversalBar(77, 77.5, 0, 129, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//Двухминутные задачи

        $pdf->addPage();

        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->writeTextBold('100%', 149.3, 28.3, 10, [255,255,255]);//Управленческие навыки

        $pdf->addUniversalBar(77.7, 48.9, 23, 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//1
        $pdf->addUniversalBar(77.7, 59.5, 98, 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//2
        $pdf->addUniversalBar(77.7, 70.1, 43, 128.7, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_POSITIVE);//3


        $pdf->addPage();
        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->writeTextBold('100%', 149.9, 28.2, 10, [255,255,255]);//1
        $pdf->writeTextBold('100%', 3.4, 36.8, 18);


        $pdf->addUniversalBar(77, 60, 23, 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.1 positive
        $pdf->addUniversalBar(77, 70.6, 100, 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.2 positive
        $pdf->addUniversalBar(77, 81.2, 43, 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//1.3 positive

        $pdf->addUniversalBar(152, 60, 23, 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.1 negative
        $pdf->addUniversalBar(152, 70.6, 98, 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.2 negative
        $pdf->addUniversalBar(152, 81.2, 23, 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//1.3 negative
        $pdf->addUniversalBar(152, 91.8, 100, 54.14, AssessmentPDF::ROUNDED_BOTH, AssessmentPDF::BAR_NEGATIVE);//1.4 negative

        $pdf->addPage();
        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->writeTextBold('100%', 149.9, 27.8, 10, [255,255,255]);//1
        $pdf->writeTextBold('100%', 2.8, 36.8, 18);

        $pdf->addUniversalBar(77, 60, 3, 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.1 positive
        $pdf->addUniversalBar(77, 70.6, 30, 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.2 positive
        $pdf->addUniversalBar(77, 81.2, 93, 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//2.3 positive

        $pdf->addUniversalBar(152, 60, 26, 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.1 negative
        $pdf->addUniversalBar(152, 70.6, 0, 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.2 negative
        $pdf->addUniversalBar(152, 81.2, 2, 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//2.3 negative

        $pdf->addPage();

        $pdf->writeTextBold($username, 3.5, 3.5, 21);
        $pdf->writeTextBold('100%', 148.7, 28, 10, [255,255,255]);//1
        $pdf->writeTextBold('100%', 2.8, 36.8, 18);

        $pdf->addUniversalBar(77, 60, 3, 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.1 positive
        $pdf->addUniversalBar(77, 70.6, 30, 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.2 positive
        $pdf->addUniversalBar(77, 81.2, 93, 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.3 positive
        $pdf->addUniversalBar(77, 91.8, 93, 71.38, AssessmentPDF::ROUNDED_LEFT, AssessmentPDF::BAR_POSITIVE);//3.4 positive

        $pdf->addUniversalBar(152, 60, 26, 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.1 negative
        $pdf->addUniversalBar(152, 70.6, 0, 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.2 negative
        $pdf->addUniversalBar(152, 81.2, 2, 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.3 negative
        $pdf->addUniversalBar(152, 91.8, 2, 54.14, AssessmentPDF::ROUNDED_RIGHT, AssessmentPDF::BAR_NEGATIVE);//3.4 negative

        $pdf->renderOnBrowser('Assessment_v2');
    }

} 