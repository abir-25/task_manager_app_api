<?php

/**
 * Created by PhpStorm.
 * User: fuad
 * Date: 4/23/18
 * Time: 2:48 PM
 */
namespace App\DataModel\Model;

class User {
    private $id;
    private $name;
    private $username;
    private $password;
    private $phone;
    private $profileImg;
    private $created_at;
    private $updated_at;
    private $status;
    private $token;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        if($this->password){
            return hash('SHA256',$this->password);
        } else {
            return $this->password;
        }
    }

    public function getPasswordWithoutHashed()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getProfileImg()
    {
        return $this->profileImg;
    }

    /**
     * @param mixed $profileImg
     */
    public function setProfileImg($profileImg): void
    {
        $this->profileImg = $profileImg;
    }

    public function getProfileImgUrl()
    {
        if($this->profileImg)
        {
            return url('/uploads/images/user/'.$this->profileImg);
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    public function mapper($data): void
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->username = (!empty($data['username'])) ? $data['username'] : null;
        $this->password = (!empty($data['password'])) ? $data['password'] : null;
        $this->name =(!empty($data['name'])) ? $data['name'] : null;
        $this->phone = (!empty($data['phone'])) ? $data['phone'] : null;
        $this->profileImg = (!empty($data['profileImg'])) ? $data['profileImg'] : null;
        $this->status = (!empty($data['status'])) ? $data['status'] : 1;
    }

    public function toJson(): array
    {
        return [
            'userId'          => $this->getId(),
            'name'            => $this->getName(),
            'phone'           => $this->getPhone(),
            'username'        => $this->getUserName(),
            'profileImgUrl'   => $this->getProfileImgUrl(),
            'status'          => $this->getStatus() ?? 1
        ];
    }
}
