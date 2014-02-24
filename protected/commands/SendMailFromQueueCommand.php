<?php

class SendMailFromQueueCommand extends CConsoleCommand {

    public function actionIndex()
    {
        $result = MailHelper::sendMailFromQueue(500);
        echo "Done - {$result['done']}\r\n";
        echo "Fail - {$result['fail']}\r\n";
    }

}