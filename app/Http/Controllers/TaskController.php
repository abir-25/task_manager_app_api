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
