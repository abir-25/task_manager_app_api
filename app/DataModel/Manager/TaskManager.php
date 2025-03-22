<?php
namespace App\DataModel\Manager;

use App\DataModel\DBManager\Database;
use App\DataModel\Model\Task;
use App\DataModel\Model\TaskArray;
use Exception;

class TaskManager
{

    /**
     * @throws Exception
     */
    public function getLastTaskPositionByStatus($data): array
    {
        $queryString = "SELECT MAX(position) AS last_position
                        FROM tasks
                        WHERE status = ? and userId = ?";
        $params      = array($data['status'], $data['userId']);
        return  (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

    /**
     * @throws Exception
     */
    public function createTask(Task $task): Task
    {
        $queryString      = "insert into tasks(userId, name, description, status, position, due_date, created_at) values(?,?,?,?,?,?,?)";
        $queryParameter   = array();
        $queryParameter[] = $task->getUserId();
        $queryParameter[] = $task->getName();
        $queryParameter[] = $task->getDescription();
        $queryParameter[] = $task->getStatus();
        $queryParameter[] = $task->getPosition();
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
    public function updateTaskStatus($activeId, $status): void
    {
        $queryString = "UPDATE tasks
                        SET status = ?, updated_at = ?
                        WHERE id = ?";
        $parameters = array($status, now(), $activeId);
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

    /**
     * @throws Exception
     */
    public function getTaskStatusReport($userId): array
    {
        $queryString = "SELECT status FROM tasks WHERE userId = ? AND
                        created_at BETWEEN DATE_FORMAT(NOW(), '%Y-%m-01') AND LAST_DAY(NOW())";
        $params      = array($userId);
        $tmpList     =  (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
        return $this->mapStatusReport($tmpList);
    }

    public function mapStatusReport($tmpList): array
    {
        $taskCounts = array_map(function ($value) {
            return 0;
        }, TaskArray::$taskStatus);

        if (!empty($tmpList)) {
            foreach ($tmpList as $task) {
                if (isset($taskCounts[$task->status])) {
                    $taskCounts[$task->status]++;
                }
            }
        }

        $result = [];
        foreach ($taskCounts as $status => $count) {
            $result[] = [
                "status" => $status,
                "count" => $count
            ];
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    public function getTaskList($data): array
    {
        $status    = $data["status"] ?? "all";
        $searchKey = $data["searchKey"] ?? null;
        $dueDate   = $data["dueDate"] ?? null;
        $whereCondition  = isset($status) && $status!=="all" ? " AND status = '$status'" : "";
        $whereCondition .= isset($dueDate) ? " AND DATE(due_date) = '" . $dueDate . "'" : "";
        $whereCondition .= isset($searchKey) && $searchKey !== "" ? " AND name LIKE '%$searchKey%'" : "";

        $queryString     = "SELECT id, userId, position, name, description, status, DATE(due_date) as dueDate FROM tasks WHERE userId = ? ".$whereCondition." ORDER BY position";
        $params          = array($data["userId"]);
        return (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

    /**
     * @throws Exception
     */
    public function getTaskStatusAndPositionById($activeId, $userId): array
    {
        $queryString = "SELECT status, position FROM tasks WHERE id = ? AND userId = ?";
        $params      = array($activeId, $userId);
        return  (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

    /**
     * @throws Exception
     */
    public function getTaskStatusAndPositionByStatus($status, $userId): array
    {
        $queryString = "SELECT MAX(position) as position FROM tasks WHERE status = ? AND userId = ?";
        $params      = array($status, $userId);
        return  (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

    /**
     * @throws Exception
     */
    public function getTaskIdsAndPositionsBetweenActiveAndOverIds($greaterPosition, $lesserPosition, $status): array
    {
        $queryString = "SELECT id, position FROM tasks WHERE status = ? AND position BETWEEN ? AND ? ORDER BY position";
        $params      = array($status, $lesserPosition, $greaterPosition);
        return  (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

    /**
     * @throws Exception
     */
    public function getTaskPositionsFromOverIdToOthers($overIdPosition, $status): array
    {
        $queryString = "SELECT id, position FROM tasks WHERE status = ? AND position > ? ORDER BY position";
        $params      = array($status, $overIdPosition);
        return  (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

    /**
     * @throws Exception
     */
    public function getTaskPositionsFromActiveIdToOthers($activeIdPosition, $status): array
    {
        $queryString = "SELECT id, position FROM tasks WHERE status = ? AND position > ? ORDER BY position";
        $params      = array($status, $activeIdPosition);
        return  (new Database())->executeQueryDataReturnWithParameter($queryString, $params) ?? [];
    }

}
