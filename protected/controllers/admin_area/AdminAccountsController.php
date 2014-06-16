<?php
/**
 * Содержит котроллеры для операций на аккаунтами пользователей
 */

class AdminAccountsController extends BaseAdminController {

    /**
     * @param integer $userId, YumUser.id
     */
    public function actionAccountVacanciesList($userId) {
        /** @var array of Vacancy $vacancies */
        $vacancies = Vacancy::model()->findAllByAttributes(['user_id' => $userId]);

        /** @var YumUser $user */
        $user = YumUser::model()->findByPk($userId);

        $this->layout = '/admin_area/layouts/admin_main';

        $this->render('/admin_area/pages/user_accounts/vacancies_list', [
            'vacancies' => $vacancies,
            'user'      => $user,
        ]);
    }

    /**
     * И добавление новой и редактирование вакансии
     *
     * @param integer $userId, YumUser.id
     */
    public function actionAddVacancy($userId) {

        /** @var YumUser $user */
        $user = YumUser::model()->findByPk($userId);

        $id = Yii::app()->request->getParam('id');
        $action = Yii::app()->request->getParam('action');

        /** @var Vacancy $vacancy */
        if (null !== $id) {
            $vacancy = Vacancy::model()->findByPk($id);
        }

        if (null == $id || null == $vacancy) {
            $vacancy = new Vacancy();
            $vacancy->attributes = Yii::app()->request->getParam('Vacancy');
        }

        // Пользователь нажал <button> "Сохранить"
        if (null != $action) {
            $vacancy->attributes = Yii::app()->request->getParam('Vacancy');
            
            if ($vacancy->validate()) {
                $vacancy->user_id = $user->id;
                $vacancy->save();
                Yii::app()->user->setFlash('success', 'Позиция "'.$vacancy->label.'" успешно обновлена (создана).');
                $this->redirect('/admin_area/user/' . $user->id . '/vacancies-list');
            }
        }

        // ---

        $specializations = StaticSiteTools::formatValuesArrayLite(
            'ProfessionalSpecialization',
            'id',
            'label',
            "",
            'Выберите специализацию'
        );

        $positionLevels = StaticSiteTools::formatValuesArrayLite(
            'PositionLevel',
            'slug',
            'label',
            '',
            'Выберите уровень позиции'
        );

        // ---

        $this->layout = '/admin_area/layouts/admin_main';

        $this->render('/admin_area/pages/user_accounts/add_vacancy', [
            'vacancy'         => $vacancy,
            'user'            => $user,
            'positionLevels'  => $positionLevels,
            'specializations' => $specializations,
        ]);
    }

    /**
     * @param integer $userId, YumUser.id
     * @param integer $vacancyId, Vacancy.id
     */
    public function actionRemoveVacancy($userId, $vacancyId) {

        /** @var YumUser $user */
        $user = YumUser::model()->findByPk($userId);

        /** @var Vacancy $vacancy */
        $vacancy = Vacancy::model()->findByPk($vacancyId);

        if ($vacancy->user_id != $user->id) {
            Yii::app()->user->setFlash('error', 'У вас нет прав для удаления этой позиции');
            $this->redirect('/admin_area/user/' . $user->id . '/vacancies-list');
        }

        // @todo: is we will keep storing deleted invites - we must exclude such invites from query
        $counter = Invite::model()->countByAttributes([
            'vacancy_id' => $vacancy->id,
        ]);

        if (0 < $counter) {
            Yii::app()->user->setFlash(
                'error',
                'Вы не можете удалить позицию "'.$vacancy->label.'", с ней уже связаны приглашения'
            );
            $this->redirect('/admin_area/user/' . $user->id . '/vacancies-list');
        }

        $vacancy->delete();

        $this->redirect('/admin_area/user/' . $user->id . '/vacancies-list');
    }

    public function actionSendInvites($userId) {

        /* @var YumUser $user */
        $user = YumUser::model()->findByPk($userId);
        $this->layout = '/admin_area/layouts/admin_main';
        $render = ['user'=>$user];
        $list = [];
        $invites = [];
        $hasErrors = false;
        $isValid = false;
        $isSend = false;
        $valid_emails = [];
        $no_valid_emails = [];
        $invite_limit_error = false;

        if( $this->getParam('valid_form') === 'true' ) {
            $isValid = true;
            $data = $this->getParam('data');
            $data['hide_result'] = isset($data['hide_result'])?$data['hide_result']:0;
            $list_email = preg_split("/[\s,]+/", $data['email'], null, PREG_SPLIT_NO_EMPTY);
            $list_first_name = preg_split("/[\s,]+/", $data['first_name'], null, PREG_SPLIT_NO_EMPTY);
            $list_last_name = preg_split("/[\s,]+/", $data['last_name'], null, PREG_SPLIT_NO_EMPTY);
            $list_iteration = count(max($list_email, $list_first_name, $list_last_name));

            for ($i = 0; $i < $list_iteration; $i++) {
                $email = isset($list_email[$i])?$list_email[$i]:'';
                if(!empty($email)) {
                    if(in_array($email, $valid_emails)){
                        $no_valid_emails[] = $email;
                    }else{
                        $valid_emails[] = $email;
                    }
                }

                $invite = new Invite();
                $invite->vacancy_id = $data['vacancy'];
                $invite->email = isset($list_email[$i])?$list_email[$i]:'';
                $invite->lastname = isset($list_last_name[$i])?$list_last_name[$i]:'';
                $invite->firstname = isset($list_first_name[$i])?$list_first_name[$i]:'';
                $invite->message = $data['message'];
                $profile = YumProfile::model()->findByAttributes(['email' => strtolower($invite->email)]);

                if($this->getParam('send_form') === 'true') {
                    $isSend = true;
                    $profile_personal = $profile;

                    if(null === $profile_personal) {
                        $password = UserService::generatePassword(8);
                        $user_personal  = new YumUser('registration');
                        $user_personal->setAttributes(['password'=>$password, 'password_again'=>$password, 'agree_with_terms'=>'yes']);
                        $profile_personal  = new YumProfile('registration');
                        $profile_personal->setAttributes([
                            'firstname' => $invite->firstname,
                            'lastname'  => $invite->lastname,
                            'email'     => $invite->email
                        ]);
                        $account_personal = new UserAccountPersonal('personal');

                        if(UserService::createPersonalAccount($user_personal, $profile_personal, $account_personal)){

                            YumUser::activate($profile_personal->email, $user_personal->activationKey);
                            try{
                                if(UserService::sendInvite($user, $invite, $data['hide_result'])){
                                    UserService::sendEmailInviteAndRegistration($invite, $password);
                                }
                            } catch(RedirectException $e) {
                                $invite_limit_error = true;

                            }
                        }
                    } else {
                        try{
                            if(UserService::sendInvite($user, $invite, $data['hide_result'])){
                                UserService::sendEmailInvite($invite);
                            }
                        } catch(RedirectException $e) {
                            $invite_limit_error = true;
                        }
                    }
                } else {
                    try{
                        UserService::sendInvite($user, $invite, $data['hide_result'], false);
                    } catch(RedirectException $e) {
                        $invite_limit_error = true;
                    }
                }
                if($invite->hasErrors()){
                    $hasErrors = true;
                }
                $invites[] = $invite;
            }

            $render['data'] = (object)$data;
        } else {
            $render['data'] = (object)['email'=>'','first_name'=>'','last_name'=>'','vacancy'=>'','hide_result'=>'','message'=>$user->account_corporate->default_invitation_mail_text];
        }

        if(count($no_valid_emails) !== 0) {
            $hasErrors = true;
        }

        $render['list'] = $list;
        $render['invites'] = $invites;
        $render['has_errors'] = $hasErrors;
        $render['isValid'] = $isValid;
        $render['isSend'] = $isSend;

        if($hasErrors) {
            if(count($no_valid_emails) !== 0) {
                Yii::app()->user->setFlash('error', 'Дублирование email-ов '.implode(', ', $no_valid_emails));
            }else{
                Yii::app()->user->setFlash('error', Yii::t('site', 'Исправьте ошибки'));
            }
        }else{
            if($isValid) {
                if($isSend){
                    Yii::app()->user->setFlash('success', Yii::t('site', 'Все приглашения отправлены в очередь писем'));
                } else {
                    if(count($invites) === 0 && $isValid) {
                        $render['has_errors'] = true;
                        Yii::app()->user->setFlash('error', Yii::t('site', 'У вас нет адресатов'));
                    }elseif($user->account_corporate->getTotalAvailableInvitesLimit() < count($invites)){
                        $invite_limit_error = true;
                    }else{
                        Yii::app()->user->setFlash('success', Yii::t('site', 'Все поля правильные'));
                    }
                }
            }
        }
        if($invite_limit_error){
            $render['has_errors'] = true;
            Yii::app()->user->setFlash('error', Yii::t('site', 'У вас недостаточно инвайтов(сейчас '.$user->account_corporate->getTotalAvailableInvitesLimit().' - нужно '.count($invites).')'));
        }
        if($invite_limit_error === false && $hasErrors === false && $isSend && $isValid) {
            UserService::logAccountAction($user, $_SERVER['REMOTE_ADDR'], 'Админ '.$this->user->profile->email.' отправил приглашения от имени '.$user->profile->email.' для '.implode(',', $valid_emails));
        }
        $this->render('//admin_area/pages/user_send_invites', $render);
    }

    public function actionBanUser($userId, $action) {
        /* @var YumUser $banUser */
        $banUser = YumUser::model()->findByPk($userId);

        if($banUser->isCorporate()) {
            if($action === 'ban') {
                $isBanned = $banUser->banUser();
                if($isBanned) {
                    UserService::logAccountAction($banUser, $_SERVER['REMOTE_ADDR'], 'Пользователь '.$banUser->profile->email.' был за банен (статус "banned") админом '.$this->user->profile->email);
                    Yii::app()->user->setFlash('success', 'Аккаунт '. $banUser->profile->email .' успешно заблокирован.');
                }
            }else{
                $isUnBanned = $banUser->unBanUser();
                if($isUnBanned) {
                    UserService::logAccountAction($banUser, $_SERVER['REMOTE_ADDR'], 'Пользователь '.$banUser->profile->email.' был разбанен админом '.$this->user->profile->email);
                    Yii::app()->user->setFlash('success', 'Аккаунт '. $banUser->profile->email .' успешно раблокирован.');
                }
            }
        }
    }

    public function actionUserAddRemoveInvitations($userId, $value)
    {
        $admin = Yii::app()->user->data();
        $user = YumUser::model()->findByPk($userId);
        if (null === $user ) {
            Yii::app()->user->setFlash('error', sprintf(
                'Не найден пользователь с номером "%s".',
                $userId
            ));
            $this->redirect('/admin_area/dashboard');
        }

        // set invites_limit {

        $user->getAccount()->changeInviteLimits($value, $admin);

        // set invites_limit }


        $this->redirect('/admin_area/user/'.$userId.'/details');
    }
} 