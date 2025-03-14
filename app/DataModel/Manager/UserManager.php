<?php
namespace App\DataModel\Manager;

use App\DataModel\DBManager\Database;
use App\DataModel\Model\User;

class UserManager
{
    public $appTokenCacheKey = "app_token";

    public function createUser(User $user): User
    {
        return $this->registerUser($user);
    }

    public function registerUser(User $user): User
    {
        $queryString      = "insert into users(username, password, status, created_at) values(?,?,?,?)";
        $queryParameter   = array();
        $queryParameter[] = $user->getUsername();
        $queryParameter[] = $user->getPassword() ?? '';
        $queryParameter[] = $user->getStatus() ?? 1;
        $queryParameter[] = now();
        $dbManager = new Database();

        $user->setId($dbManager->executeQueryInsert($queryString, $queryParameter));
        return $user;
    }

    public function setCacheAppToken($userId, $token): void
    {
        $cacheManager = new CacheManager();
        $tokenList= $cacheManager->getFromCache($this->appTokenCacheKey);
        $tokenList[$userId]=$token;
        $cacheManager->setToCacheForever($this->appTokenCacheKey,$tokenList);
    }

    private function getUserInfo($userId)
    {
        $userInfo = $this->getUserInfoFromCache($userId);
        if ($userInfo == null) {
            return $this->updateUserInfoInCache($userId);
        }
        return $userInfo;
    }

    private function getUserInfoFromCache($userId)
    {
        $key = "user_info_" . $userId;
        return (new CacheManager())->getFromCache($key);
    }

    public function updateUserInfoInCache($userId): User
    {
        $userInfo = $this->mapUserInfo($this->getUserInfoByUserId($userId));
        $this->setUserInfoInCache($userId, $userInfo);
        return $userInfo;
    }

    private function mapUserInfo($userInfo): User
    {
        $userInfo = reset($userInfo);
        $user = new User();
        $user->setId($userInfo['userId']);
        $user->setName($userInfo['name']);
        $user->setUserName($userInfo['username']);
        $user->setStatus($userInfo['status'] ?? 1);
        return $user;
    }

    private function getUserInfoByUserId($userId): array|string
    {
        $queryString = "SELECT us.name, us.userId, us.userType, us.username, us.profile_img, us.userEmail, us.userPhoneNo, sur.roleId FROM users us LEFT JOIN shop_users_roles sur ON us.userId = sur.userId WHERE us.userId=?";

        $params = array($userId);
        return (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

    public function getUserInfoByUsername($username): ?array
    {
        $queryString = "SELECT id, name, username, status, password FROM users  WHERE username=?";
        $params = array($username);
        return (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

    private function setUserInfoInCache($userId, $value): void
    {
        $key = "user_info_" . $userId;
        (new CacheManager())->setToCacheForever($key, $value);
    }
}
