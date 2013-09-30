<?php

class m130930_193030_update_non_corpurate_emails extends CDbMigration
{
	public function up()
	{
        $domains = [
            'drdrb.com',
            '10minutemail.com',
            'suioe.com',
            '10minutemail.net',
            'guerrillamail.com',
            'my10minutemail.com',
            'guerrillamailblock.com',
            'sharklasers.com',
            'guerrillamail.net',
            'guerrillamail.org',
            'guerrillamail.biz',
            'spam4.me',
            'grr.la',
            'guerrillamail.de   ',
            '10minutemail.davidxia.com',
            'davidxia.com',
            'spamgoes.in ',
            'mailinator.com',
            'meltmail.com',
            'tempemail.net',
            'dunflimblag.mailexpire.com',
            'filzmail.com',
            '20minutemail.com',
            'mt2014.com',
            'thankyou2010.com',
            'thankyou2010.com',
            'trash2009.com',
            'mt2009.com',
            'trashymail.com',
            'mytrashmail.com',
            'mailmetrash.com',
            'spambox.us',
            'maileater.com',
            'tempomail.fr',
            'pookmail.com',
            'spamfree24.org',
            'spammotel.com',
            'emaildiscussions.com',
            'spamspot.com',
            'spam.la',
            'email.bugmenot.com',
            'bugmenot.com',
            'incognitomail.org',
            'makeuseof.com',
            'tothepc.com',
            'checknew.pw',
            'sheepskinproxy.com',
            'temp-mail.ru',
            'thismail.ru',
            'mailspeed.ru ',
            'kakvse.net',
            'onservis.ru',
            'thismail.ru',
            'it-snacks.info',
            'tempinbox.com',
            'mailforspam.com',
            'tmpmail.ru ',
            'spambog.com',
            'live.com',
            'yopmail.com',
        ];

        foreach ($domains as $domain) {
            $provider = FreeEmailProvider::model()->findByAttributes(['domain' => $domain]);

            if (null === $provider) {
                $provider = new FreeEmailProvider();
                $provider->domain = $domain;
                $provider->save();
            }
        }
	}

	public function down()
	{
		echo "m130930_193030_update_non_corpurate_emails does not support migration down.\n";
	}
}