<?php
namespace App\DataModel\Manager;

use App\DataModel\DBManager\Database;
use App\DataModel\Model\User;

class UserManager
{
    public $appTokenCacheKey = "app_token";

    /**
     * @throws \Exception
     */
    public function createUser(User $user): User
    {
        return $this->registerUser($user);
    }

    /**
     * @throws \Exception
     */
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

    /**
     * @throws \Exception
     */
    public function getUserInfo($userId)
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

    /**
     * @throws \Exception
     */
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
        $user->setId($userInfo->id);
        $user->setName($userInfo->name);
        $user->setUserName($userInfo->username);
        $user->setPhone($userInfo->phone);
        $user->setProfileImg($userInfo->profileImg);
        $user->setStatus($userInfo->status ?? 1);
        return $user;
    }

    /**
     * @throws \Exception
     */
    private function getUserInfoByUserId($userId): array
    {
        $queryString = "SELECT id, name, username, phone, profileImg, status FROM users
                        WHERE id=?";
        $params = array($userId);
        return (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

    /**
     * @throws \Exception
     */
    public function getUserInfoByUsername($username): ?array
    {
        $queryString = "SELECT id, name, username, password, phone, profileImg, status FROM users WHERE username=?";
        $params = array($username);
        return (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

    public function setUserInfoInCache($userId, $value): void
    {
        $key = "user_info_" . $userId;
        (new CacheManager())->setToCacheForever($key, $value);
    }

    /**
     * @throws \Exception
     */
    public function updateUserInfo(User $user): void
    {
        $queryString = "UPDATE users SET name = ?, phone = ?, updated_at = ?
                        WHERE id = ?";
        $parameters = array($user->getName(), $user->getPhone(), now(), $user->getId());
        (new Database())->executeQueryWithParameter($queryString, $parameters);
    }

    /**
     * @throws \Exception
     */
    public function updateUserProfileImg($userId, $img)
    {
        $queryString = "UPDATE users SET profileImg = ?, updated_at = ? WHERE id = ?";
        $parameters = array($img, now(), $userId);
        (new Database())->executeQueryWithParameter($queryString, $parameters);
    }
}
