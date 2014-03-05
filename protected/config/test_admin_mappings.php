<?php
/**
 * Example: Yii::app()->params['test_admin_mappings']['dialogs']['key'][2]
 */
return [

    'login_page' => [
        'email' => 'css=#YumUserLogin_username',
        'password' => 'css=#YumUserLogin_password',
        'enter' => 'css=.btn.btn-large.btn-primary'
    ],

    'pages_list' => [
        'home' => "xpath=//*[@id='yw1']/li[1]/a",
        'invites' => "xpath=//*[@id='yw1']/li[2]/a",
        'simulations' => "xpath=//*[@id='yw1']/li[3]/a",
        'rating' => "xpath=//*[@id='yw1']/li[4]/a",
        'all_users' => "xpath=//*[@id='yw1']/li[6]/a",
        'corporate_users' => "xpath=//*[@id='yw1']/li[7]/a",
        'orders' => "xpath=//*[@id='yw1']/li[8]/a",
        'review' => "xpath=//*[@id='yw1']/li[9]/a",
        'subscription' => "xpath=//*[@id='yw1']/li[10]/a",
        'log_auth' => "xpath=//*[@id='yw1']/li[11]/a",
        'admin_list' => "xpath=//*[@id='yw1']/li[12]/a",
        'blocked_auth' => "xpath=//*[@id='yw1']/li[13]/a",
        'simulations_now' => "xpath=//*[@id='yw1']/li[15]/a",
        'imports' => "xpath=//*[@id='yw1']/li[16]/a",
        'statistics' => "xpath=//*[@id='yw1']/li[17]/a",
        'emails_queue' => "xpath=//*[@id='yw1']/li[18]/a",
        'registrations' => "xpath=//*[@id='yw1']/li[19]/a",
        'free_emails_list' => "xpath=//*[@id='yw1']/li[20]/a"
    ],

    'home_page' => [
        'dev_lite_sim' => 'xpath=//div[2]/div/div[2]/div/a[1]',
        'dev_full_sim' => 'xpath=//div[2]/div/div[2]/div/a[2]',
        'add_10_sims' => 'xpath=//div[2]/div/div[2]/div/a[3]',
        'quick_view_email' => 'xpath=//div[2]/div/div[2]/div/form[1]/input[1]',
        'quick_view_find' => 'xpath=//div[2]/div/div[2]/div/form[1]/input[2]',
        'current_user_details' => "xpath=//div[1]/div/div/div/p/a[1]",
        'logout'=>"xpath=//div[1]/div/div/div/p/a[2]",
    ],

    'corporate_info' => [
        'change_password' => "xpath=//div[2]/div/div[2]/a[1]",
        'account_ban' => "xpath=//div[2]/div/div[2]/a[2]",
        'enter_as_this_user' => "xpath=//div[2]/div/div[2]/a[3]",
        'account_logs' => "xpath=//div[2]/div/div[2]/a[4]",
        'auth_block' => "xpath=//div[2]/div/div[2]/a[5]",
        'invites_for_me' => "xpath=//div[2]/div/div[2]/a[6]",
        'invites_logs' => "xpath=//div[2]/div/div[2]/a[7]",
        'invites_from_me' => "xpath=//div[2]/div/div[2]/a[8]",
        'send_mass_invites' => "xpath=//body/div[2]/div/div[2]/a[9]",
        'name' => "xpath=//div[2]/div/div[2]/table/tbody/tr[1]/td[2]/span[1]",
        'surname' => "xpath=//div[2]/div/div[2]/table/tbody/tr[1]/td[2]/span[2]",
        'registration_date' => "xpath=//div[2]/div/div[2]/table/tbody/tr[2]/td[2]",
        'email' => "xpath=//div[2]/div/div[2]/table/tbody/tr[3]/td[4]",
        'last_visit_date' => "xpath=//div[2]/div/div[2]/table/tbody/tr[2]/td[4]",
        'account_type' => "xpath=//div[2]/div/div[2]/table/tbody/tr[3]/td[2]/span[1]",
        'is_account_blocked' => "xpath=//div[2]/div/div[2]/table/tbody/tr[3]/td[2]/span[2]",
        'sim_amount' => "xpath=//div[2]/div/div[2]/table/tbody/tr[4]/td[2]",
        'add_sim_amount' => "xpath=//div[2]/div/div[2]/table/tbody/tr[5]/td[2]/form/input[1]",
        'add_sim_amount_btn' => "xpath=//*[@id='add_invites_button']",
        'type_of_rating' => "xpath=//div[2]/div/div[2]/table/tbody/tr[6]/td[2]",
        'ip_address' => "xpath=//div[2]/div/div[2]/table/tbody/tr[7]/td[2]",
        'discount_value' => "xpath=//div[2]/div/div[2]/form[1]/input[1]",
        'discount_first_date' => "xpath=//div[2]/div/div[2]/form[1]/input[2]",
        'discount_last_date' => "xpath=//div[2]/div/div[2]/form[1]/input[3]",
        'discount_change' => "xpath=//div[2]/div/div[2]/form[1]/button",
        'site' => "xpath=//div[2]/div/div[2]/form[2]/table/tbody/tr[2]/td[1]/textarea",
        'company_description' => "xpath=//div[2]/div/div[2]/form[2]/table/tbody/tr[2]/td[2]/textarea",
        'telephones' => "xpath=//div[2]/div/div[2]/form[2]/table/tbody/tr[4]/td[1]/textarea",
        'company_name' => "xpath=//div[2]/div/div[2]/form[2]/table/tbody/tr[6]/td[1]/textarea",
        'company_branch' => "xpath=//div[2]/div/div[2]/form[2]/table/tbody/tr[6]/td[2]/textarea",
        'save_info_about_company' => "xpath=//div[2]/div/div[2]/form[2]/table/tbody/tr[7]/td[2]/button"
    ],

    'personal_info' => [
        '' => '',
    ],
];