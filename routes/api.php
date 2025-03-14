<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountController as Account;
use App\Http\Controllers\TaskController as Task;

Route::get('/', function () {
    return "All right reserved to Mohammad Abir Mahmud\n";
});

Route::group(['prefix' => '/v1'], function () {
    Route::post('/register',[Account::class,'postRegisterAction']);
    Route::post('/login',[Account::class,'postLoginAction']);

    Route::group(['middleware'=>[ 'appToken' ]],function () {
        Route::post('/create-task',[Task::class,'postCreateTaskAction']);
        Route::post('/update-task',[Task::class,'postUpdateTaskAction']);
        Route::post('/update-task-status',[Task::class,'postUpdateTaskStatusAction']);
        Route::get('/get-task-list/{status?}/{dueDate?}',[Task::class,'getTaskList']);
        Route::get('/get-task-info/{taskId}',[Task::class,'getTaskInfoById']);
        Route::get('/search-task/{keyword}',[Task::class,'getSearchTask']);
        Route::post('/delete-task',[Task::class,'postDeleteTaskAction']);
    });
});
