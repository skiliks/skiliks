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
}


