<?php

class SendMailFromQueueCommand extends CConsoleCommand {

    public function actionIndex()
    {
        $result = MailHelper::sendMailFromQueue();
        echo "Done - {$result['done']}\r\n";
        echo "Fail - {$result['fail']}\r\n";
    }

}