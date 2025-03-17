<?php
namespace App\DataModel\Manager;

use App\DataModel\DBManager\Database;
use App\DataModel\Model\Task;
use Exception;

class TaskManager
{
    /**
     * @throws Exception
     */
    public function createTask(Task $task): Task
    {
        $queryString      = "insert into tasks(userId, name, description, status, due_date, created_at) values(?,?,?,?,?,?)";
        $queryParameter   = array();
        $queryParameter[] = $task->getUserId();
        $queryParameter[] = $task->getName();
        $queryParameter[] = $task->getDescription();
        $queryParameter[] = $task->getStatus();
        $queryParameter[] = $task->getDueDate();
        $queryParameter[] = now();
        $dbManager = new Database();

        $task->setId($dbManager->executeQueryInsert($queryString, $queryParameter));
        return $task;
    }

    /**
     * @throws Exception
     */
    public function updateTaskInfo(Task $task): void
    {
        $queryString = "UPDATE tasks
                        SET userId = ?, name = ?, description = ?, status = ?, due_date = ?, updated_at = ?
                        WHERE id = ?";
        $parameters = array($task->getUserId(), $task->getName(), $task->getDescription(),
            $task->getStatus(), $task->getDueDate(), now(), $task->getId());
        (new Database())->executeQueryWithParameter($queryString, $parameters);
    }

    /**
     * @throws Exception
     */
    public function updateTaskStatus($task): void
    {
        $queryString = "UPDATE tasks
                        SET status = ?, updated_at = ?
                        WHERE id = ?";
        $parameters = array($task->getStatus(), now(), $task->getId());
        (new Database())->executeQueryWithParameter($queryString, $parameters);
    }

    /**
     * @throws Exception
     */
    public function deleteTask($id): void
    {
        $queryString = "DELETE FROM tasks WHERE id = ?";
        $parameters = array($id);
        (new Database())->executeQueryWithParameter($queryString, $parameters);
    }

}
