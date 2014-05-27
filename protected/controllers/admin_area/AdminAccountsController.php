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
                Yii::app()->user->setFlash('success', 'Позиция "'.$vacancy->label.'" успешно обнослена (создана).');
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

} 