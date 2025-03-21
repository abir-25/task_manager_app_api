<?php

/**
 * Created by PhpStorm.
 * User: fuad
 * Date: 4/23/18
 * Time: 2:48 PM
 */
namespace App\DataModel\Model;

class Task {
    private $id;
    private $userId;
    private $name;
    private $description;
    private $status;
    private $dueDate;
    private $position;
    private $created_at;
    private $updated_at;

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
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = $userId;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
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
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position): void
    {
        $this->position = $position;
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


    public function toJson(): array
    {
        return [
            'taskId'          => $this->getId(),
            'userId'          => $this->getUserId(),
            'name'            => $this->getName(),
            'description'     => $this->getDescription(),
            'status'          => $this->getStatus(),
            'position'        => $this->getPosition(),
            'dueDate'         => $this->getDueDate()
        ];
    }

    public function mapper($data): void
    {
        $this->id          = (!empty($data['id'])) ? $data['id'] : null;
        $this->userId      = (!empty($data['userId'])) ? $data['userId'] : null;
        $this->name        = (!empty($data['name'])) ? $data['name'] : null;
        $this->description = (!empty($data['description'])) ? $data['description'] : null;
        $this->status      = (!empty($data['status'])) ? $data['status'] : 1;
        $this->position    = (!empty($data['position'])) ? $data['position'] : 0;
        $this->dueDate     = (!empty($data['dueDate'])) ? $data['dueDate'] : 1;
    }
}
