<?php



/**
 * Сервис по работе с пользователями
 *
 * @author Sergey Suzdaltsev <sergey.suzdaltsev@gmail.com>
 */
class UserService {
    
    /**
     * Получить список групп пользователя 
     * @param int $uid 
     * @return array
     */
    public static function getGroups($uid) {
        $userGroupsCollection = UserGroupsModel::model()->byUser($uid)->findAll();
        $groups = array();
        foreach($userGroupsCollection as $group) {
            $groups[] = $group->gid;
        }
       
        if (count($groups) > 0) {
            $groupsCollection = GroupsModel::model()->byIds($groups)->findAll();
        }
        else $groupsCollection = array();
        
        $groups = array();
        $groups[1] = 'promo';
        foreach($groupsCollection as $group) {
            $groups[$group->id] = $group->name;
        }
        
        return $groups;
    }
    
    public static function addGroupToUser($uid, $gid) {
        $userGroups = new UserGroupsModel();
        $userGroups->uid = $uid;
        $userGroups->gid = $gid;
        $userGroups->insert();
    }
    
    /**
     * Проверяет является ли пользователь членом заданной группы
     * @param int $uid
     * @param int $gid
     * @return bool
     */
    public static function isMemberOfGroup($uid, $gid) {
        return (1 === (int)UserGroupsModel::model()->byUser($uid)->byGroup($gid)->count());
    }
    
    /**
     * @param string $email
     * @param string $password
     * @param string $passwordAgain
     * 
     * @return string|boolean
     */
    public static function validateNewUserData($email, $password, $passwordAgain)
    {
        if (Users::model()->byEmail($email)->isActive()->find()) {
            return sprintf("Пользователь с емейлом %s уже существует", $email);
        }
        
        if ($password != $passwordAgain) {    
            return 'Введенные пароли не совпадают';
        }
        
        return true;
    }
    
    public static function registerUser($email, $password)
    {
        $user = new Users();
        $user->password    = $user->encryptPassword($password);
        $user->email       = $email;
        $user->is_active   = 1;
        try {
            $user->insert();
        } catch (Exception $e) {
            Yii::log($e->getMessage());
            Yii::log($e->getTraceAsString());
            return null;
        }

        $activationCode            = $user->generateActivationCode();
        $usersActivationCode       = new UsersActivationCode();
        $usersActivationCode->uid  = $user->id;
        $usersActivationCode->code = $user->activationCode;
        $usersActivationCode->insert();

        // Добавить группы пользователей
        UserService::addGroupToUser($user->id, 1);
        
        return $user;
    }
}


