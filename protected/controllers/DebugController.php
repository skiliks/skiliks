<?php

class DebugController extends SiteBaseController
{

    public function actionIndex() {
       $var = Scenario::model()->findByAttributes(['slug'=>Scenario::TYPE_FULL])->getOutgoingPhoneTheme(['theme_id'=>3]);
       var_dump($var);
    }

    public function actionSend()
    {
        foreach (['anjela', 'denejnaia', 'fikus', 'jeleznij', 'krutko', 'trudiakin'] as $template) {

            $mailOptions           = new SiteEmailOptions();
            $mailOptions->from     = 'support@skiliks.com';
            $mailOptions->to       = 'slavka@skiliks.com';
            $mailOptions->subject  = 'New emails markup test. 11.3';
            $mailOptions->template = $template;
            $mailOptions->text1    = 'Text 1. lkj lk jlk jl kj lkj kj lj lkj lj lkj lkjl kjl jl jl jlkj lj lkj lkj lkj lkj lkj lkj lkj ljl jl kjl kjl kj';
            $mailOptions->text2    = 'Text 2. lkj lk jlk jl k kljl kjl jlk jlj ljlk jlkj lj lj ljl jl jl jl kj lkj kj lj lkj lj lkj lkjl kjl jl jl jlkj l jlkj ljl kjl jlkj ljl jlj ljl jl jl jl jlj ljlj lkj lkj lkj lkj lkj lkj lkj ljl jl kjl kjl kj';

            UserService::addStandardEmailToQueue($mailOptions, $template);

            $result = MailHelper::sendMailFromQueue();
            echo "Done - {$result['done']}\r\n";
            echo "Fail - {$result['fail']}\r\n";
        }
    }
}

