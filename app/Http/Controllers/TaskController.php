<?php

namespace App\Http\Controllers;

use App\DataModel\DBManager\Database;
use App\DataModel\Manager\SessionManager;
use App\DataModel\Manager\TaskManager;
use App\DataModel\Manager\UserManager;
use App\DataModel\Model\Task;
use App\DataModel\Model\User;
use App\DataModel\Model\TaskArray;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use function App\Helpers\globalResponse;

class TaskController extends Controller
{
    private int $successStatusCode  = 200;
    private int $unauthorizedAction = 401;
    private int $unprocessedEntity  = 442;
    private int $errorStatusCode    = 500;
    private int $notFound           = 404;

    public function __construct()
    {}

    public function postCreateTaskAction(Request $request): \Illuminate\Http\JsonResponse
    {
        $data        = $request->all();
        $task        = new Task();
        $taskManager = new TaskManager();

        try {
            $rules = [
                'userId'  => 'required',
                'name'    => 'required',
                'status'  => 'required',
                'dueDate' => 'required',
            ];
            $customMessages = [
                'userId.required'  => "User ID is required. Without user ID task will be confused!",
                'name.required'    => "Task name is essential. Let’s not leave it blank like a mystery novel!",
                'status.required'  => "Status is essential. We have no idea about this tasks' status!",
                'dueDate.required' => "Tasks need deadlines, not dreams. Set a due date!",
            ];
            $validator = \Validator::make($request->all(), $rules, $customMessages);

            if ($validator->fails()) {
                return globalResponse([], $validator->errors()->first(), false, $this->errorStatusCode);
            }
            $task->mapper($data);

            if (!array_key_exists($data["status"], TaskArray::$taskStatus)) {
                throw new Exception("Invalid status value: ".$data["status"]);
            }

            $taskPosition = $taskManager->getLastTaskPositionByStatus($data);
            if(count($taskPosition)>0){
                $task->setPosition($taskPosition[0]->last_position + 1);
            } else{
                $task->setPosition(1);
            }
            $task = $taskManager->createTask($task);
            $taskInfo = $task->toJson();
            return globalResponse($taskInfo, "Congrats! Your task is created successfully", true, $this->successStatusCode);
        } catch (\Exception $ex){
            return globalResponse([], $ex->getMessage(), false, $this->errorStatusCode);
        }
    }

    public function postUpdateTaskAction(Request $request): \Illuminate\Http\JsonResponse
    {
        $data           = $request->all();
        $task           = new Task();
        $taskManager    = new TaskManager();
        $data["userId"] = $data["user"];
        $task->mapper($data);

        try {
            if (!array_key_exists($data["status"], TaskArray::$taskStatus)) {
                throw new Exception("Invalid status value: ".$data["status"]);
            }

            $taskManager->updateTaskInfo($task);
            $taskInfo = $task->toJSON();

            return globalResponse($taskInfo, "Congrats! Your task is updated successfully", true, $this->successStatusCode);
        } catch (\Exception $ex){
            return globalResponse([], $ex->getMessage(), false, $this->errorStatusCode);
        }
    }

    public function postUpdateTaskStatusAction(Request $request): \Illuminate\Http\JsonResponse
    {
        $data           = $request->all();
        $task           = new Task();
        $taskManager    = new TaskManager();
        $task->mapper($data);

        try {
            if (!array_key_exists($data["status"], TaskArray::$taskStatus)) {
                throw new Exception("Invalid status value: ".$data["status"]);
            }

            $taskManager->updateTaskStatus($task);
            $taskInfo = $task->toJSON();

            return globalResponse($taskInfo, "Congrats! Your task status is updated successfully", true, $this->successStatusCode);
        } catch (\Exception $ex){
            return globalResponse([], $ex->getMessage(), false, $this->errorStatusCode);
        }
    }

    public function postUpdateTaskPositionAction(Request $request): \Illuminate\Http\JsonResponse
    {
        $data           = $request->all();
        $taskManager    = new TaskManager();
        $database       = new Database();
        try {
            $activeIdStatusAndPosition = $taskManager->getTaskStatusAndPoistionById($data["activeId"], $data["userId"]);
            $overIdStatusAndPosition   = $taskManager->getTaskStatusAndPoistionById($data["overId"], $data["userId"]);
            if(count($activeIdStatusAndPosition)>0 && count($overIdStatusAndPosition)>0){
                $updateRows = [];

                if($activeIdStatusAndPosition[0]->status == $overIdStatusAndPosition[0]->status){
                    $direction       = $activeIdStatusAndPosition[0]->position > $overIdStatusAndPosition[0]->position ? "up" : "down";
                    $greaterPosition = max($activeIdStatusAndPosition[0]->position, $overIdStatusAndPosition[0]->position);
                    $lesserPosition  = min($activeIdStatusAndPosition[0]->position, $overIdStatusAndPosition[0]->position);

                    $taskIdAndPositions = $taskManager->getTaskIdsAndPositionsBetweenActiveAndOverIds($greaterPosition, $lesserPosition, $activeIdStatusAndPosition[0]->status);
                    $filteredItems = array_values(array_filter($taskIdAndPositions, fn($item) => $item->id !== $data["activeId"]));
                    if($direction=="down"){
                        $activeIdNewPosition = 0;
                        foreach ($filteredItems as $item)
                        {
                            if($item->id == $data["overId"]){
                                $activeIdNewPosition = $item->position;
                            }
                            $updateRows[$item->id] = [$item->position - 1];
                        }
                        $updateRows[$data["activeId"]] = [$activeIdNewPosition];
                    } else{
                        $activeIdNewPosition = 0;
                        foreach ($filteredItems as $item)
                        {
                            if($item->id == $data["overId"]){
                                $activeIdNewPosition = $item->position;
                            }
                            $updateRows[$item->id] = [$item->position + 1];
                        }
                        $updateRows[$data["activeId"]] = [$activeIdNewPosition];
                    }
                } else{

                }

                if (count($updateRows) > 0) {
                    $ids = array_keys($updateRows);
                    $updateQuery = $database->bulk_update_sql_statement(
                        'tasks',
                        'id',
                        'position',
                        $updateRows,
                        $ids
                    );
                    $database->executeQuery($updateQuery);
                }
            }
            return globalResponse([], "Congrats! Your task position is updated", true, $this->successStatusCode);
        } catch (\Exception $ex){
            return globalResponse([], $ex->getMessage(), false, $this->errorStatusCode);
        }
    }

    public function postDeleteTaskAction(Request $request): \Illuminate\Http\JsonResponse
    {
        $data           = $request->all();
        $taskManager    = new TaskManager();

        try {
            $taskManager->deleteTask($data['id']);

            return globalResponse([], "Congrats! Your task is deleted successfully", true, $this->successStatusCode);
        } catch (\Exception $ex){
            return globalResponse([], $ex->getMessage(), false, $this->errorStatusCode);
        }
    }

    public function getTaskStatusReportAction(Request $request): \Illuminate\Http\JsonResponse
    {
        $data           = $request->all();
        $taskManager    = new TaskManager();

        try {
            $taskStatusReport = $taskManager->getTaskStatusReport($data['userId']);
            return globalResponse($taskStatusReport, "Sir! Your task report is ready", true, $this->successStatusCode);
        } catch (\Exception $ex){
            return globalResponse([], $ex->getMessage(), false, $this->errorStatusCode);
        }
    }

    public function getTaskList(Request $request): \Illuminate\Http\JsonResponse
    {
        $data           = $request->all();
        $taskManager    = new TaskManager();

        try {
            $taskList = $taskManager->getTaskList($data);
            return globalResponse($taskList, "Task list fetched successfully", true, $this->successStatusCode);
        } catch (\Exception $ex){
            return globalResponse([], $ex->getMessage(), false, $this->errorStatusCode);
        }
    }

}
