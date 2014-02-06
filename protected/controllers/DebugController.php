<?php

class DebugController extends SiteBaseController
{

    public function actionIndex() {
       $var = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL])->getOutgoingPhoneTheme(['theme_id'=>3]);
       var_dump($var);
    }

    public function actionSend()
    {
        $email_to = Yii::app()->request->getParam('email', 'slavka@skiliks.com');
        //$email_to = Yii::app()->request->getParam('email', 'gty1991@gmail.com');

        $text = '<br/>Text 2. <br/>2lkj lk jlk jl k klj<br/>3l kjl jlk jl<br/>4j ljlk <br/>5jlkj <br/>6lj lj ljl jl jl<br/>7 jl kj lkj<br/>8 kj lj lkj lj<br/>9 lkj lkjl kjl jl <br/>10jl jlkj<br/>11 l jlkj l<br/>12jl kjl jlkj ljl11<br/>13 jlj ljl jl jl jl jlj ljlj lkj lkj lkj lkj lkj lkj lkj ljl jl kjl kjl kj'
            .'Text 2. lkj      lk jlk jl koookljl kjl jlk jlj ljlk jlkj lj lj ljl jl jl jl kj lkj kj lj lkj lj lkj lkjl kjl jl jl jlkj l jlkj ljl kjl jlkj ljl jlj ljl jl jl jl jlj ljlj lkj lkj lkj lkj lkj lkj lkj ljl jl kjl kjl kj'
            .'Text 2. lkj lk jlk jl k kljl kjl jlk jlj ljlk jlkj lj lj ljl jl jl jl kj lkj kj lj lkj lj lkj lkjl kjl jl jl jlkj l jlkj ljl kjl jlkj ljl jlj ljl jl jl jl jlj ljlj lkj lkj lkj lkj lkj lkj lkj ljl jl kjl kjl kj'
            .'Text 2. lkj lk jlk jl k kljl kjl jlk jlj ljlk jlkj lj lj ljl jl jl jl kj lkj kj lj lkj lj lkj lkjl kjl jl jl jlkj l jlkj ljl kjl jlkj ljl jlj ljl jl jl jl jlj ljlj lkj lkj lkj lkj lkj lkj lkj ljl jl kjl kjl kj'
            .'Text 2. lkj lk jlk jl k kljl kjl jlk jlj ljlk jlkj lj lj ljl jl jl jl kj lkj kj lj lkj lj lkj lkjl kjl jl jl jlkj l jlkj ljl kjl jlkj ljl jlj ljl jl jl jl jlj ljlj lkj lkj lkj lkj lkj lkj lkj ljl jl kjl kjl kj'
            .'Text 2. lkj lk jlk jl k kljl kjl jlk jlj ljlk jlkj lj lj ljl jl jl jl kj lkj kj lj lkj lj lkj lkjl kjl jl jl jlkj l jlkj ljl kjl jlkj ljl jlj ljl jl jl jl jlj ljlj lkj lkj lkj lkj lkj lkj lkj ljl jl kjl kjl kj';


        foreach (['anjela'/*, 'denejnaia', 'fikus', 'jeleznij', 'krutko', 'trudiakin'*/] as $template) {

            $mailOptions           = new SiteEmailOptions();
            $mailOptions->from     = 'support@skiliks.com';
            $mailOptions->to       = $email_to;
            $mailOptions->subject  = 'New emails markup test. 12.2 - '.$template.' - '.date('H:i:s');
            $mailOptions->template = $template;
            $mailOptions->setText($text);

            UserService::addStandardEmailToQueue($mailOptions, $template);

            echo 'done';

            $result = MailHelper::sendMailFromQueue(1);
            echo "Done - {$result['done']}\r\n";
            echo "Fail - {$result['fail']}\r\n";
        }
    }

    public function actionStandard() {
        $this->layout = '//layouts/site_standard_2';

        $this->render('//static/site/error404', []);
    }

    public function actionClusterData() {
        $this->layout = null;
        $result = UserService::getServerInfo();
        echo "IP code: ".$result['ip_code']."\r\n <br>";
        echo "IP db: ".$result['ip_db'];
    }

    public function actionClusterData() {
        $this->layout = null;
        $result = UserService::getServerInfo();
        echo "IP code: ".$result['ip_code']."\r\n <br>";
        echo "IP db: ".$result['ip_db'];
    }
}

